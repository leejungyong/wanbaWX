// pages/game/admin/taskDetail.js
Page({

  /**
   * 页面的初始数据
   */
  data: {
    qarr: ['人工判定', '回答精确匹配答案', '回答模糊匹配答案', '答案包含在回答里', '回答包含在答案里', '教练提交管理员判定', 'N选3'],
    quesObj:{},
    qtype:null,
    memo:'',
    pics:null,
    media:'',
    url:null,
    answer:''
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {

    console.log(JSON.parse(options.ops) )
    let ques = JSON.parse(options.ops)
    this.setData({
      quesObj: ques,
      qtype:ques.qtype,
      memo:ques.memo,
      pics:ques.pics,
      media:ques.media,
      url:ques.url,
      answer:ques.answer
    })
  },
    apply(){
      let ops=this.data.quesObj
    let pages = getCurrentPages()
    let prepage = pages[pages.length - 3]
    let poiInfo = prepage.data.poiInfo
      let index = ops.qtype
    poiInfo.memo = ops.memo
    poiInfo.answer = ops.answer
    poiInfo.qtype = ops.qtype
    poiInfo.pics = ops.pics
    poiInfo.media = ops.media
    poiInfo.url = ops.url
    console.log(poiInfo)
    prepage.setData({
      poiInfo: poiInfo,
      index: index,
      qtype: this.data.qarr[index],
      memo: ops.memo,
      answer:ops.answer
    })
      wx.navigateBack({
        delta: 2
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