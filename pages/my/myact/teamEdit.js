// pages/my/myact/teamEdit.js
const app=getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    teamObj:null,
    
    colorData: {
      //基础色相，即左侧色盘右上顶点的颜色，由右侧的色相条控制
      hueData: {
        colorStopRed: 255,
        colorStopGreen: 0,
        colorStopBlue: 0,
      },
      //选择点的信息（左侧色盘上的小圆点，即你选择的颜色）
      pickerData: {
        x: 0, //选择点x轴偏移量
        y: 480, //选择点y轴偏移量
        red: 0,
        green: 0,
        blue: 0,
        hex: '#000000'
      },
      //色相控制条的位置
      barY: 0
    },
    rpxRatio: 1 //此值为你的屏幕CSS像素宽度/750，单位rpx实际像素
  },
  uploadPic(){
    wx.chooseImage({
      count:1,
      sourceType: ['album', 'camera'],
      success: function(res) {
        const tempFilePaths = res.tempFilePaths
        wx.uploadFile({
          url: '',
          filePath: tempFilePaths[0],
          name: 'file',
          success:res=>{
            console.log(res)
          }
        })
      },
    })
  },

  saveEdit(){
    let that=this
    console.log(that.data.teamObj)
    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=editTeamByThemeId',
      method:'POST',
      data:{
        obj:that.data.teamObj
      },
      success:res=>{
        console.log(res.data)
      }
    })
  },
  changeMemo(e){
    let memo=e.detail.value
    let obj=this.data.teamObj
    obj.desc=memo
    this.setData({
      teamObj:obj
    })
  },
  changeType(e){
    let obj=this.data.teamObj
    obj.name=e.detail.value
    this.setData({
      teamObj:obj
    })
    
  },
  //选择改色时触发（在左侧色盘触摸或者切换右侧色相条）
  onChangeColor(e) {
    //返回的信息在e.detail.colorData中
    this.setData({
      colorData: e.detail.colorData
    })
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    let obj=JSON.parse(options.teamObj)
    this.setData({
      teamObj:obj
    })
    //设置rpxRatio
    // wx.getSystemInfo({
    //   success(res) {
    //     _this.setData({
    //       rpxRatio: res.screenWidth / 750
    //     })
    //   }
    // })
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