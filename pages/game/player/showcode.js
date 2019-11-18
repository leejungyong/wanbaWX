var QR = require('../../../utils/wxqrcode.js');
var openid, teamid, act, aid, token=null
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
    let that = this
    let ops=JSON.parse(options.ops)
    aid = ops.aid
    openid=ops.openid
    token = new Date().getTime();
    var param = 'act=' + ops.act + '&openid=' +ops.openid + '&teamid=' + ops.teamid + '&token=' + token + '&aid=' + ops.aid+'&taskid='+ops.taskid+'&sellprice='+ops.sellprice+'&posid='+ops.posid+'&teamname='+ops.teamname
    var size = this.setCanvasSize(); //动态设置画布大小
    
    console.log(param)
   // that.createQrCode(param, "mycanvas", size.w, size.h)
    setTimeout(()=>{
      that.createQrCode(param, "mycanvas", size.w, size.h)
    },500)
    
    if(ops.act=='isCaptain'){
       this.setData({
         height: size.w,
         width: size.w,
         teamname:ops.teamname,
         msg:'请向游戏管理员出示二维码'
       })
    } else if (ops.act == 'sell'){
      this.setData({
        msg: '请向对方出示二维码完成交易'
      })
    } else if (ops.act == 'auction'){
      this.setData({
        msg: '请向游戏管理员出示二维码完成拍卖'
      })
    }
   if (ops.act == 'isCaptain'){
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
      url: app.globalData.config.apiUrl+'index.php?act=listenScanResult',
      data: {
        openid: openid,
        aid:aid
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