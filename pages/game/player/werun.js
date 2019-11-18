var aid, teamid = 0
const app = getApp()
Page({


  data: {
    myStep: null,
    teamStep: null,
    allStep: null,
    target:100000,
    imgUrl:app.globalData.config.imgUrl
  },
  syncWeRunData(aid, teamid) {
    let that = this
    wx.showLoading({
      title: '加载中',
      mask: true
    })
    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=updateMyStep',
      data: {
        openid: wx.getStorageSync('openid'),
        aid: aid,
        teamid: teamid,
        step: that.data.myStep
      },
      method: 'POST',
      success(res) {
        console.log(res.data)
        let data = res.data
        let list = res.data.teamsteplist
         console.log(list)
        let rank = 1
        let count = 1
        list[0].rank = 1
        for (let i = 0; i < list.length - 1; i++) {

          if (list[i + 1].step == list[i].step) {
            list[i + 1].rank = rank
            count++
          }
          else {
            //rank++
            rank += count
            count = 1
            list[i + 1].rank = rank
          }
        }
        that.setData({
          teamsteplist: list,
          allStep: data.allstep,
          myStep:data.mystep
          
        })
        let rate = 2 * data.allstep/that.data.target
        that.draw(rate)
        wx.hideLoading()
      },
      fail(res) {
        wx.hideLoading()
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },
  teamRunData() {
    wx.navigateTo({
      url: '../teamRunData/teamRunData?aid=' + aid,
    })
  },
  getWeRunData() {
    let that = this
    wx.showLoading({
      title: '加载中',
      mask: true
    })
    wx.getWeRunData({ //解密微信运动
      success(res) {
        //console.log(res)
        const wRunEncryptedData = res.encryptedData
        let data = {
          iv: res.iv,
          encryptedData: wRunEncryptedData,
          session_key: wx.getStorageSync('session_key')
        }
         //console.log(data)
        wx.request({
          url: app.globalData.config.apiUrl+'decrypt/decrypt.php',
          data: data,
          method: 'POST',
          success(res) {
            wx.hideLoading()
            // console.log(res)
            let data = JSON.parse(res.data)
            let myStep = data.stepInfoList

            if (myStep) {
              let todayStep = myStep[myStep.length - 1].step
              that.setData({
                myStep: todayStep
              });
              // console.log(aid,teamid)
              if (aid > 0) {
                setTimeout(function () {

                  that.syncWeRunData(aid, teamid)
                }, 100)
              }
            }

          },
          fail(err) {
            wx.hideLoading()
            console.log(err)
          }
        })
      },
      fail(err) {
        wx.hideLoading()
        wx.navigateTo({
          url: './authwerun',
        })
      }
    })
  },
  draw(step){
    // 页面渲染完成
    var cxt_arc = wx.createCanvasContext('canvasArc');//创建并返回绘图上下文context对象。
    cxt_arc.setLineWidth(15);
    cxt_arc.setStrokeStyle('#24435d');
    cxt_arc.setLineCap('round')
    cxt_arc.beginPath();//开始一个新的路径
    cxt_arc.arc(140, 140, 125, 0, 2 * Math.PI, false);
    cxt_arc.stroke();
    cxt_arc.setLineWidth(3);
    cxt_arc.setStrokeStyle('#24435d');
    cxt_arc.setLineCap('round')
    cxt_arc.beginPath();//开始一个新的路径
    cxt_arc.arc(140, 140, 105, 0, 2 * Math.PI, false);
    cxt_arc.stroke();//对当前路径进行描边
    cxt_arc.setLineWidth(3);
    cxt_arc.setStrokeStyle('#1a90d3');
    cxt_arc.setLineCap('round')
    cxt_arc.beginPath();//开始一个新的路径
    cxt_arc.arc(140, 140, 105, -Math.PI * 1 / 2, step * Math.PI - Math.PI / 2, false);
    cxt_arc.stroke();//对当前路径进行描边
    cxt_arc.draw();
  },
  onLoad: function (options) {
    let ops=JSON.parse(options.ops) 
    aid=ops.aid
    teamid=ops.teamid
    //console.log(aid, teamid)
    this.getWeRunData()
    
  }
})