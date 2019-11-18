var aid, act_title = null
const app = getApp()
Page({
  data: {
    stone1: 0,
    stone2: 0,
    stone3: 0,
    stone4: 0,
    stone5: 0,
    stone6: 0,
    stone7: 0,
    maxNum: 10,
    cat: -1,
    posFlag:true,
    posValue:1,
  },
  toggle(e){
    let v = e.detail.value
    //console.log(v)
    this.setData({
      posValue: v.length===0 ? 0:1
    })
    console.log(this.data.posValue)
  },
  slider1change(e) {
    this.setData({
      stone1: e.detail.value
    })
  },
  slider2change(e) {
    this.setData({
      stone2: e.detail.value
    })
  },
  slider3change(e) {
    this.setData({
      stone3: e.detail.value
    })
  },
  slider4change(e) {
    this.setData({
      stone4: e.detail.value
    })
  },
  slider5change(e) {
    this.setData({
      stone5: e.detail.value
    })
  },
  slider6change(e) {
    this.setData({
      stone6: e.detail.value
    })
  },
  slider7change(e) {
    this.setData({
      stone7: e.detail.value
    })
    //console.log(this.data)
  },
  batchMakeStone() {
    let that = this
    if (this.data.stone1 > 0 || this.data.stone2 > 0 || this.data.stone3 > 0 || this.data.stone4 > 0 || this.data.stone5 > 0 || this.data.stone6 > 0 || this.data.stone7 > 0) {
     // console.log(this.data)
      wx.showModal({
        title: '提示',
        content: '确定要生成宝石吗？',
        success(res) {
          if (res.confirm) {
            //console.log('用户点击确定')

            wx.request({
              url: app.globalData.config.apiUrl + 'index.php?act=batchMakeStone',
              data: {
                aid: aid,
                stones: that.data
              },
              method: 'POST',
              success: (res) => {
                let data = res.data
                console.log(data)
                if (data.status) {
                  let pages = getCurrentPages()
                  let prepage = pages[pages.length - 2]
                  prepage.fetch()
                  // wx.showToast({
                  //   title: data.msg,
                  //   icon:'none'
                  // })
                  that.setData({
                    stone1: 0,
                    stone2: 0,
                    stone3: 0,
                    stone4: 0,
                    stone5: 0,
                    stone6: 0,
                    stone7: 0
                  })
                  wx.showModal({
                    title: '提示',
                    showCancel: false,
                    content: data.msg,
                    success(res) {
                      wx.navigateBack()
                    }
                  })
                  // setTimeout(()=>{
                  //   wx.navigateBack()
                  // },2000)
                }

              },
              fail: (res) => {
                wx.showToast({
                  title: '网络错误',
                  icon: 'none'
                })
              }

            })
          } else if (res.cancel) {
            console.log('用户点击取消')
          }
        }
      })


    } else {
      wx.showToast({
        title: '请设置宝石数量',
        icon: 'none'
      })
    }
  },


  onLoad: function(options) {
    aid = options.aid
    act_title = options.act_title
    
   this.setData({
      cat: options.cat
    })
  },
})