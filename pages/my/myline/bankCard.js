// pages/my/myline/bankCard.js
var app=getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    uploadUrl: app.globalData.config.uploadUrl,
    currentCard:'',
    cardList:[]
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
      url: app.globalData.config.apiUrl+'index.php?act=getMyBankCardList',
      method:'POST',
      data:{
        openid:wx.getStorageSync('openid')
      },
      success:res=>{
        console.log(res.data)
        if(res.data){
          that.setData({
            cardList:res.data
          })
        }
      }
    })
  },
  /** 跳转到添加银行卡界面 */
  toAddCard(){
  
    wx.navigateTo({
      url: './addCard',
    })
  },
  /** 跳转到提现 */
  toCashOut(e){
    let index=e.currentTarget.dataset.idx
    let that=this
    // console.log(e)
    wx.navigateTo({
      url: './cashOut?bank='+that.data.cardList[index].bank+'&cardnum='+that.data.cardList[index].cardno+'&rawcardno='+that.data.cardList[index].rawcardno+'&name='+that.data.cardList[index].name,
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