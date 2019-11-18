var d = new Date()
var year = d.getFullYear()
var month = d.getMonth() + 1 
var fullmonth= month<10 ? '0'+month :month
var app = getApp()
Page({

  data: {
    value: '全部',
    index: 0,
    date: year + '-' + fullmonth,
    typeArray: ['全部', '收益','消费'],

    list: [],
    year: new Date().getFullYear(),
    month: new Date().getMonth() + 1,
    mypoint:0
  },

  /**绑定类型选择 */
  bindTypeChange(e) {
    this.setData({
      index: e.detail.value
    })
    this.fetch()
  },
  /**绑定时间选择 */
  bindDateChange(e) {
    let str = e.detail.value
    let arr = str.split('-')
    console.log(arr[1].replace(/\b(0+)/gi, ""))
    this.setData({
      date: e.detail.value,
      year: arr[0],
      month: arr[1].replace(/\b(0+)/gi, "")
    })
    this.fetch()
  },

  fetch() {
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getMyAccountBalance',
      method: 'POST',
      data: {
         openid: wx.getStorageSync('openid'),
         token: '',
        type: that.data.index,
        year: that.data.year,
        month: that.data.month
      },
      success: res => {
        let data=res.data
        
        that.setData({
          list: data.list,
          mypoint: data.account.point ? data.account.point : 0,
        })
      }
    })
  },


  onLoad: function (options) {
    this.fetch()
  },

})