// pages/my/myline/addCard.js
var app=getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    name:'',
    bankCardNum:''
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {

  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {

  },
  name(e){
    this.setData({
      name:e.detail.value
    })
  },
  bankCardNum(e){
    this.setData({
      bankCardNum:e.detail.value
    })
  },
/** 点击下一步按钮 */
next(){
  // console.log(this.data.name)
  let that=this
  if(that.data.name==''||that.data.bankCardNum==''){
    wx.showModal({
      title: '提示',
      content: '姓名或银行卡号不能为空！',
      showCancel:false
    })
  }else{
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=checkBankCard',
      method: 'POST',
      data: {
        openid:wx.getStorageSync('openid'),
        card: that.data.bankCardNum,
        name:that.data.name
      },
      success: res => {
        console.log(res.data)
        if (res.data.status) {
          wx.navigateTo({
            url: './cashOut?bank='+res.data.bank+'&rawcardno='+res.data.cardno+'&name='+res.data.name+'&cardnum='
          })
        } else {
          wx.showModal({
            title: '',
            content: res.data.msg,
            showCancel: false
          })
          // that.setData({
          // })

        }
      }
    })
  }

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