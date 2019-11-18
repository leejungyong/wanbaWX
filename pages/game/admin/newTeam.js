// pages/my/myact/teamEdit.js
const app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    aid: null,
    themeid: null,
    displayorder: null,
    name: '',
    desc: '',
    pic: '',
    color: '#ffffff',
    img: '',
    index: null,
    teamnum: null,
    showColorPicker: false,
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
  showPicker() {
    let that = this
    that.setData({
      showColorPicker: !that.data.showColorPicker
    })
  },
  onChangeColor(e) {
    const index = e.target.dataset.id

    this.setData({
      colorData: e.detail.colorData,
      color: e.detail.colorData.pickerData.hex
    })

  },
  uploadPic() {
    let that = this
    wx.chooseImage({
      count: 1,
      sourceType: ['album', 'camera'],
      success: function (res) {
        const tempFilePaths = res.tempFilePaths
        that.setData({
          img: tempFilePaths[0]
        })

      },
    })
  },

  changeMemo(e) {
    let memo = e.detail.value
    this.setData({
      desc: memo
    })
  },
  changeType(e) {
    this.setData({
      name: e.detail.value
    })

  },
  sureAdd() {

    let that = this
    let pages = getCurrentPages()
    let prePage = pages[pages.length - 2]
    let list = prePage.data.list

    let obj = {
      aid: that.data.aid,
      themeid: that.data.themeid,
      displayorder: that.data.displayorder,
      name: that.data.name,
      desc: that.data.desc,
      pic: that.data.pic,
      color: that.data.color,
    }
    if (list.length < that.data.teamnum) {
      list.push(obj)
      wx.request({
        url: app.globalData.config.apiUrl + 'index.php?act=addDefinedTeam',
        method: 'POST',
        data: {
          aid: that.data.aid,
          list: list
        },
        success: res => {
          console.log(res.data)

          if (res.data.status) {
            let arr = res.data.list
            wx.uploadFile({
              url: app.globalData.config.apiUrl + 'uploadteampic.php',
              filePath: that.data.img,
              name: 'file',
              formData: {
                teamid: res.data.list[res.data.list.length - 1].id,
                openid: wx.getStorageSync('openid')
              },
              success: res => {
                console.log(res.data)
                let data = JSON.parse(res.data)
                arr[arr.length - 1].pic = data.pic
                prePage.setData({
                  list: arr
                })
              }
            })

            wx.navigateBack({
              delta: 1
            })
            wx.showLoading({
              title: '添加成功！',
            })
            wx.hideLoading()
          }


        }
      })
    }else{
      wx.showModal({
        title: '提示',
        content: '你的活动设置中限定队伍数量不超过'+that.data.teamnum+'个！',
        showCancel:false
      })
    }

  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {

    let _this = this
    _this.setData({
      aid: options.aid,
      themeid: options.themeid,
      displayorder: options.displayorder,
      teamnum: options.teamnum
    })
    wx.getSystemInfo({
      success(res) {
        _this.setData({
          rpxRatio: res.screenWidth / 750
        })
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