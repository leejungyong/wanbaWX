// pages/my/myline/billList.js
var app=getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    date: '',
    year: new Date().getFullYear(),
    month: new Date().getMonth() + 1,
    list:[],
    aid:'',
    title:''
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    let that = this
    let str = that.data.month < 10 ? (that.data.year + '-0' + that.data.month) : (that.data.year + '-' + that.data.month)
      that.setData({
        date: str,
        aid:options.aid,
        title:options.title
      })
      that.fetchData()
  },
  /**绑定时间选择 */
  bindDateChange(e) {
    let str = e.detail.value
    console.log(str)
    let arr = str.split('-')
    console.log(arr[1].replace(/\b(0+)/gi, ""))
    this.setData({
      date: e.detail.value,
      year: arr[0],
      month: arr[1].replace(/\b(0+)/gi, "")
    })
    this.fetchData()
  },
  fetchData(){
    let that=this
    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=getMyRouteMonthBill',
      method:'POST',
      data:{
        aid:that.data.aid,
        year:that.data.year,
        month:that.data.month
      },
      success:res=>{
        console.log(res.data)
        if(res.data){
          that.setData({
            list: res.data
          })
        }
      }
    })
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