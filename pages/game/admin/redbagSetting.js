var aid=null
const app = getApp()
Page({


  data: {
    sum:-1,
    total: '',
    rand: ''
  },


  onLoad: function (options) {
   aid=options.aid
   this.fetch(aid)
  },
  back(){
    wx.navigateBack()
  },
  fetch(aid) {


    wx.showLoading({
      title: '加载中',
    })
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=actSetting',
      data: {
        aid: aid,
        openid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        wx.hideLoading()

        

        this.setData({
          sum:data.act.redbagsum,
         total: data.act.redbagtotal,
         rand:data.act.redbagrand
        })
        console.log(this.data)
      },
      fail: (err) => {
        wx.hideLoading()
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },
  updateTotal: function (e) {
    let total = e.detail.value
    
    this.setData({
      total: parseInt(total)
    })

  },
  updateRand(e) {
    let rand = e.detail.value
    this.setData({
      rand: rand
    })
  },

  confirm() {
    let that = this
    let rand = this.data.rand.split('|')
    let arr=[]
     //console.log(rand)
     for(let i in rand){
       let rnd=parseInt(rand[i])
       console.log(rnd)
       if (rnd == '' || isNaN(parseInt(rnd)) || parseInt(rnd) <=0) {
         wx.showToast({
           title: '请正确设置单个红包随机金额',
           icon: 'none'
         })
         return
       }
      arr.push(rnd)
     }
     this.setData({
       rand:arr.join('|')
     })
   
    let total = parseInt(this.data.total)
   // console.log(total)
    if (total == NaN  || total < 0 ) {
      wx.showToast({
        title: '请设置红包总额',
        icon: 'none'
      })
      return
    }
   


    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=postRedbagSetting',
      data: {
        aid:aid,
        redbagtotal:that.data.total,
        redbagrand: that.data.rand
      },
      method: 'POST',
      success: (res) => {
       let data = res.data
       console.log(data)
        wx.showToast({
          title: data.msg,
        })
        setTimeout(()=>{
          wx.navigateBack()
        },2000)
      },
      fail: (res) => {
         wx.showToast({
           title: '网络错误',
           icon:'none'
         })
      }
    })
  },
})