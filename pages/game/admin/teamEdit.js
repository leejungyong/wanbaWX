// pages/my/myact/teamEdit.js
const app = getApp()
var pic = ''
Page({

  /**
   * 页面的初始数据
   */
  data: {
    aid: null,
    teamObj: null,
    index: null,
    showColorPicker: false,
    img: '',
    actmode:'',
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
    let obj = this.data.teamObj
    obj.color = e.detail.colorData.pickerData.hex
    this.setData({
      colorData: e.detail.colorData,
      teamObj: obj
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

  saveEdit() {
    let that = this
    wx.showLoading({
      title: '保存中...',
    })
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=editTeamById',
      method: 'POST',
      data: {
        obj: that.data.teamObj
      },
      success: res => {
        // console.log(res.data)

        let team = res.data.data

        let pages = getCurrentPages()
        let prePage = pages[pages.length - 2]
        let list = prePage.data.list
        let stamp = new Date().getTime()
        if (pic != that.data.img) {
          wx.uploadFile({
            url: app.globalData.config.apiUrl + 'uploadteampic.php',
            filePath: that.data.img,
            name: 'file',
            formData: {
              teamid: that.data.teamObj.id,
              openid: wx.getStorageSync('openid')
            },
            success: res => {
              console.log(res)
              let data = JSON.parse(res.data)
              team.pic = data.pic + '?' + stamp
            }
          })
        }


        that.setData({
          teamObj: team
        })
        list[that.data.index] = team

        let str = list[that.data.index].pic

        if (str.indexOf('jpg') > -1) {

          list[that.data.index].pic = str.substring(0, str.indexOf('jpg') + 3)
          console.log(list[that.data.index].pic)
        }

        prePage.setData({
          list: list,
          stamp: stamp
        })

        wx.navigateBack({
          delta: 1
        })
        wx.hideLoading()
      }
    })
  },

  delete() {
    let that = this
    let pages = getCurrentPages()
    let prePage = pages[pages.length - 2]
    let list = prePage.data.list
    if(list.length>1){
      list.splice(that.data.index, 1)
      wx.showModal({
        title: '删除确认',
        content: '确认删除该队伍吗？',
        success: res => {
          wx.request({
            url: app.globalData.config.apiUrl + 'index.php?act=delTeamById',
            method: 'POST',
            data: {
              aid: that.data.aid,
              id: that.data.teamObj.id,
              list: list
            },
            success: res => {
              console.log(res.data)
              if (res.data.status) {
                wx.showLoading({
                  title: '删除成功！',
                })

                prePage.setData({
                  list: res.data.list
                })

                wx.navigateBack({
                  delta: 1
                })

                wx.hideLoading()

              }
            }
          })
        }
      })
    }else{
      wx.showModal({
        title: '提示',
        content: '请至少保留一个队伍！',
        showCancel:false
      })
    }

  },
  changeMemo(e) {
    let memo = e.detail.value
    let obj = this.data.teamObj
    obj.desc = memo
    this.setData({
      teamObj: obj
    })
  },
  changeType(e) {
    let obj = this.data.teamObj
    obj.name = e.detail.value
    this.setData({
      teamObj: obj
    })

  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    console.log(options)
    let obj = JSON.parse(options.teamObj)
    obj.pic = obj.pic.replace(/？/, '?')
    pic = obj.pic
    this.setData({
      teamObj: obj,
      index: options.index,
      aid: options.aid,
      img: obj.pic,
      actmode:options.actmode
    })
    console.log(this.data.img)
    let _this = this
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