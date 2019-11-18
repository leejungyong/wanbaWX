// pages/my/myline/billList.js
var app=getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    date:'',
    year: new Date().getFullYear(),
    month: new Date().getMonth() + 1,
    showDate:false,
    type:'全部',
    index:0,
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    let that=this
    let str = that.data.month < 10 ? (that.data.year + '-0' + that.data.month ): (that.data.year + '-' + that.data.month)
    that.setData({
      date:str
    })
    that.fetchData()
  },

  /**选择全部还是按月 */
  chooseType(){
    let that=this
    let itemList = ['全部', '按月']
    wx.showActionSheet({
      itemList: itemList,
      success(res) {

        if(res.tapIndex==0){
          that.setData({
            showDate:false,
            type:itemList[res.tapIndex],
            index:res.tapIndex
          })
          that.fetchData()
        }else if(res.tapIndex==1){
          that.setData({
            showDate:true,
            type: itemList[res.tapIndex],
            index:res.tapIndex
          })
          that.fetchData()
        }
      },
      fail(res) {
        console.log(res.errMsg)
      }
    })
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
    wx.showLoading({
      title: '加载中...',
    })
    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=getAllMyRouteMonthBill',
      method:'POST',
      data:{
        //openid:'oO4Qc5GSMkagYw0NsHa4dFZU7FLE',
        openid:wx.getStorageSync('openid'),
        year:that.data.year,
        month:that.data.month,
        flag:that.data.index
      },
      success:res=>{
        console.log(res.data)
        that.setData({
          list:res.data
        })
        wx.hideLoading()
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