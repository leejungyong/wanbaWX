var aid, list = null
const app = getApp()
Page({
  data: {
    num1:0,
    num2: 0,
    num3: 0,
    pvalue:300,
    maxNum: 10,
    total:0
  },
  slider1change(e) {
    
    let num1 = e.detail.value
    let num2 = this.data.num2
    let num3 = this.data.num3
    let pvalue = this.data.pvalue
    let total = pvalue * 3 * num1 * 2 + pvalue * 5 * num2 * 3 + pvalue * 7 * num3 * 5
    this.setData({
      num1:num1,
      total:total
    })
  },
  slider2change(e) {
    let num2 = e.detail.value
    let num1 = this.data.num1
    let num3 = this.data.num3
    let pvalue = this.data.pvalue
    let total = pvalue * 3 * num1 * 2 + pvalue * 5 * num2 * 3 + pvalue * 7* num3 * 5
    this.setData({
      num2: num2,
      total: total
    })
  },
  slider3change(e) {
    let num3 = e.detail.value
    let num2 = this.data.num2
    let num1 = this.data.num1
    let pvalue = this.data.pvalue
    let total = pvalue * 3 * num1 * 2 + pvalue * 5 * num2 * 3 + pvalue * 7 * num3 * 5
    this.setData({
      num3: num3,
      total: total
    })
  },

  postScore() {
    let that = this
    if (this.data.num1 > 0 || this.data.num2 > 0 || this.data.num3 > 0) {
      wx.showModal({
        title: '提示',
        content: '确定要进行拍卖结算吗？',
        success(res) {
          if (res.confirm) {
            //console.log('用户点击确定')

            wx.request({
              url: app.globalData.config.apiUrl + 'index.php?act=postAuctionScore',
              data: {
                aid: aid,
                num1:that.data.num1,
                num2: that.data.num2,
                num3: that.data.num3,
                teamid: that.data.displayorder,
                total:that.data.total,
                name:that.data.name
              },
              method: 'POST',
              success: (res) => {
                let data = res.data
                console.log(data)
                wx.showToast({
                  title: data.msg,
                  icon: 'none',
                  mask: true
                })
                if (data.status) {
                  let pages = getCurrentPages()
                  let prepage = pages[pages.length - 2]
                  prepage.fetch()
                  
                  setTimeout(()=>{
                    wx.navigateBack()
                  },2000)
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
        title: '请评定连线数量',
        icon: 'none'
      })
    }
  },


  onLoad: function (options) {
    aid = options.aid
   list= JSON.parse(options.ops) 
   this.setData({
     num1:list.num1,
     num2: list.num2,
     num3: list.num3,
     displayorder:list.displayorder,
     name:list.name
   })
  },
})