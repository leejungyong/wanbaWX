var QR = require('../../../utils/wxqrcode.js');
var openid, teamid, act, aid, token,taskid,posid,teamname=null
const app = getApp()
Page({


  data: {
    canvasHidden: false,
    timer: '',
    msg:'',
    width:250,
    height:250
  },


  onLoad: function (options) {
   // console.log(options)
    // openid = options.openid
    // teamid = options.teamid
    // act = options.act
    
    let ops=JSON.parse(options.ops)
    //console.log(ops)
    aid = ops.aid
    openid=ops.openid
    token = new Date().getTime();
    act=ops.act
    teamid=ops.teamid
    taskid=ops.taskid
    posid=ops.posid
    teamname=ops.teamname
    var size = this.setCanvasSize(); //动态设置画布大小
    var param = 'act=' + ops.act + '&openid=' +ops.openid + '&teamid=' + ops.teamid + '&token=' + token + '&aid=' + ops.aid+'&taskid='+ops.taskid+'&sellprice='+ops.sellprice+'&posid='+ops.posid+'&teamname='+ops.teamname
    //console.log(param)
    this.createQrCode(param, "mycanvas", size.w, size.h);
    //this.createQrCode(param, "mycanvas", 250, 250);
    if (ops.act == 'checktask' || ops.act == 'addMoney'){
       this.setData({
         height: size.w,
         width: size.w,
         teamname:ops.teamname,
         msg:'请向教练出示二维码'
       })
    } 
    let that = this
    if (ops.act == 'checktask' || ops.act == 'addMoney'){
    that.data.timer = setInterval(() => {
      that.listenScanResult()
    }, 2000)
    }
  },
  init(){

  },
  //适配不同屏幕大小的canvas
  setCanvasSize: function () {
    var size = {};
    try {
      var res = wx.getSystemInfoSync();
      var scale = 750 / 686; //不同屏幕下canvas的适配比例；设计稿是750宽
      var width = res.windowWidth / scale*.8;
      //console.log(width)
      var height = width; //canvas画布为正方形
     // console.log(height)
      size.w = width;
      size.h = height;
    } catch (e) {
      // Do something when catch error
      console.log("获取设备信息失败" + e);
    }
    return size;
  },
  createQrCode: function (url, canvasId, cavW, cavH) {
    //调用插件中的draw方法，绘制二维码图片
    QR.api.draw(url, canvasId, cavW, cavH);
    //setTimeout(() => { this.canvasToTempImage(); }, 1000);

  },
  listenScanResult() {
    let that=this
    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=listenCheckTaskResult',
      data: {
        openid: openid,
        aid:aid,
        act:act,
        teamid:teamid,
        token:token,
        taskid:taskid,
        posid:posid,
        teamname:teamname

      },
      method: 'POST',
      success: function (res) {
        let data = res.data
        console.log(res)
        if (data.status) {
         
          clearInterval(that.data.timer)
          wx.showToast({
            title: data.msg,
            icon: 'none'
          })
          setTimeout(() => {
            wx.reLaunch({
              url: './main?aid=' + aid 
            })
          }, 2000)
        }
        else {
          
        }
      },
      fail: (res) => {
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },
  onUnload() {
    let that = this
    clearInterval(that.data.timer)

  }
})