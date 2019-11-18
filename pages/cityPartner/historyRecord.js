// pages/cityPartner/historyRecord.js
var app=getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    value:'开发客户',
    index:1,
    date:'2019-04',
    typeArray:['开发客户','销售次数'],

    list:[],
    year:new Date().getFullYear(),
    month:new Date().getMonth()+1

  },

  /**绑定类型选择 */
  bindTypeChange(e){
    this.setData({
      index:e.detail.value
    })
    this.fetch()
  },
  /**绑定时间选择 */
  bindDateChange(e){
    let str=e.detail.value
    let arr=str.split('-')
    console.log(arr[1].replace(/\b(0+)/gi, ""))
    this.setData({
      date:e.detail.value,
      year:arr[0],
      month: arr[1].replace(/\b(0+)/gi, "")
    })
    this.fetch()
  },

  fetch(){
    let that=this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getMyAgentHistory',
      method: 'POST',
      data: {
        openid: wx.getStorageSync('openid'),
        token: '',
        type:that.data.index,
        year: that.data.year,
        month: that.data.month
      },
      success: res => {
        let list = [{
          title: 'eeeee',
          nick: 'aaaa',
          date: '2019-5-5'
        },
          {
            title: 'eeeee',
            nick: 'aaaa',
            date: '2019-5-5'
          }]
        console.log(res)
        that.setData({
          list:res.data
          // list:list
        })
      }
    })
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.fetch()
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {

  }
})