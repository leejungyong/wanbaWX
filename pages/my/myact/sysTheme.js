// pages/my/myact/sysTheme.js

const app=getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    swiper:null,
    circular: true,
    indicatorDots: false,
    indicatorcolor: "#000",
    vertical: false,
    autoplay: true,
    interval: 2500,
    duration: 100,
    imgheights: [],
    imgwidth: 750,
    current: 0,
    imgwidth: 0,
    imgheight: 160,
    list:null,
    url:app.globalData.config.cdn
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.fetch()
  },
  to: function (e) {
    let that = this
    let id = e.currentTarget.id
    let url = that.data.swiper[id].url
    if (url.indexOf('/pages/') > -1) {
      wx.navigateTo({
        url: url
      })
    } else {
      wx.navigateTo({
        url: '/pages/index/showWx?url=' + url
      })
    }
  },
  imageLoad: function(e) { //获取图片真实宽度  
    var imgwidth = e.detail.width,
      imgheight = e.detail.height,
      //宽高比  
      ratio = imgwidth / imgheight;
    //console.log(imgwidth, imgheight)
    //计算的高度值  
    var viewHeight = 750 / ratio;
    var imgheight = viewHeight;
    var imgheights = this.data.imgheights;
    //把每一张图片的对应的高度记录到数组里  
    imgheights[e.target.dataset.id] = imgheight;
    this.setData({
      imgheights: imgheights
    })
  },

  bindchange: function(e) {
    // console.log(e.detail.current)
    this.setData({
      current: e.detail.current
    })
  },
  toDetailPage(e){
    let that=this
    let index=e.currentTarget.dataset.idx
    wx.navigateTo({
      url: './themeDetail?themeid='+that.data.list[index].id+'&themename='+that.data.list[index].title
    })
  },
  fetch(){
    wx.showLoading({
      title:'加载中'
    })

    let that=this
    wx.request({
      url:app.globalData.config.apiUrl+'index.php?act=getSysTeamThemesList',
      data:{},
      method:'POST',
      success:res=>{
        let data=res.data
        console.log(data)
        that.setData({
          swiper:data.swiper,
          list:data.list
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