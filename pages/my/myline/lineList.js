// pages/my/myline/lineList.js
const app=getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    uploadUrl: app.globalData.config.cdn,
    list:[],
    amount:null
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.fetchData()
  },
  fetchData(){
    let that=this
    wx.request({
      url: app.globalData.config.apiUrl+ 'index.php?act=getMyRouteList',
      method:'POST',
      data:{
        // openid:'oO4Qc5GSMkagYw0NsHa4dFZU7FLE'
        openid:wx.getStorageSync('openid')
      },
      success:(res)=>{
        console.log(res.data)
        let arr=res.data
        that.setData({
          list:arr.list,
          amount:arr.amount
        })
        // console.log(arr)
      }
    })
  },
  /** 跳转至提现界面 */
  toApplyRecord(){
    wx.navigateTo({
       url:'./applyRecord?amount='+this.data.amount
    })
  },
  /** 跳转至账单界面 */
  toBill(){
    wx.navigateTo({
      url: './billList',
    })
  },
  /** 跳转至线路账单界面 */
  toLineBill(e){
    let index=e.currentTarget.dataset.idx
    let that=this
    wx.navigateTo({
      url: './linebill?aid=' + that.data.list[index].aid + '&title=' + that.data.list[index].title,
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