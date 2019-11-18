// pages/my/myact/themeDetail.js
const app=getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
      themeid:null,
      themeName:'',
      list:null,
      themeObj:{}
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {

    this.setData({
      themeid:options.themeid,
      themeName:options.themename
    })
    this.fetch()
  },

  fetch(){
    let that=this
    wx.request({
      url:app.globalData.config.apiUrl+ 'index.php?act=getThemeDetailById',
      method:'POST',
      data:{
        themeid:that.data.themeid
      },
      success:res=>{
        console.log(res.data)
        that.setData({
          list:res.data.list,
          themeObj:res.data.data
        })
        
      }
    })
  },  

  // toEdit(e){

  //   let index=e.currentTarget.dataset.idx
  //   let obj=this.data.list[index]

  //   obj.desc=obj.desc.replace('?','？')
  //   obj.desc=obj.desc.replace('&','')

  //   wx.navigateTo({
  //     url: './teamEdit?teamObj='+JSON.stringify(obj),
  //   })
  // },
  use(){
    let that=this
    let pages=getCurrentPages()
    let prePage=pages[pages.length-3]

    prePage.setData({
      themeId: that.data.themeid,
      themeName: that.data.themeName
    })

    wx.navigateBack({
      delta:2
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