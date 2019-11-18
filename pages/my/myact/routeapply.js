const app = getApp()
import regeneratorRuntime from '../../../utils/runtime.js'
import {
  wxRequest
} from '../../../utils/wxrequest.js'

var aid, currentTab, id = 0
Page({


  data: {
    imgUrl: app.globalData.config.imgUrl,
    title: '',
    memo1: '',
    memo2: '',
    memo3: '',
    pic: ''
  },
  updateTitle(e) {
    this.setData({
      title: e.detail.value
    })
  },
  updateMemo1(e) {
    this.setData({
      memo1: e.detail.value
    })
  },
  updateMemo2(e) {
    this.setData({
      memo2: e.detail.value
    })
  },
  updateMemo3(e) {
    this.setData({
      memo3: e.detail.value
    })
  },
  chooseImg() {
    let that = this
    let pic = that.data.pic

    wx.chooseImage({
      count: 1,
      sizeType: ['compressed'],
      sourceType: ['album', 'camera'],
      success: function(res) {


        pic = res.tempFilePaths[0]

        that.setData({
          pic: pic
        })
        console.log(that.data.pic)
      }
    })
  },
  delPic() {
    this.setData({
      pic: ''
    })
  },
  preview() {
    let pics = []
    pics.push(this.data.pic)
    wx.previewImage({
      urls: pics,
    })
  },

  async save() {
    let that = this
    return await wxRequest(
      app.globalData.config.apiUrl + 'index.php?act=postRouteApply', {
        hideLoading: true,
        data: {
          uid: wx.getStorageSync('openid'),
          route_desc: {
            memo1: that.data.memo1,
            memo2: that.data.memo2,
            memo3: that.data.memo3,
          },
          title: that.data.title,
          aid: aid
        }
      }
    )
  },
  beforePost() {
    let that = this

    if (that.data.memo1 == '' || that.data.memo2 == '' || that.data.memo3 == '' || that.data.title == '' || that.data.pic == '') {
      wx.showToast({
        title: '*号为必填项',
        icon: 'none',
        mask: true
      })
      return false
    } else if (wx.getStorageSync('lastpost')) {
      let token = new Date().getTime();
      let duration = token - wx.getStorageSync('lastpost')
      wx.setStorageSync('lastpost', token)
      if (duration < 3000) {
        wx.showToast({
          title: '手速有点过快呀，休息下，过几秒再点击吧',
          icon: 'none',
          mask: true
        })
        return false
      }

    }
    that.save()
      .then((ret) => {
       // console.log(ret)
        let data = ret

       // console.log(ret)
        if (data.status) {
          let routeid = data.routeid
          let pic = that.data.pic
          if (pic.indexOf('http://tmp/') > -1 || pic.indexOf('wxfile://') > -1) {

            wx.uploadFile({
              url: app.globalData.config.apiUrl + 'uploadroutepic.php',
              filePath: pic,
              name: 'file',
              formData: {
                'routeid': routeid,
                'openid': wx.getStorageSync('openid')
              },
              success: function(res) {
                let data = res.data
                 wxRequest(
                  app.globalData.config.apiUrl + 'index.php?act=updateRoutePic', {
                    hideLoading: true,
                    data: {
                      routeid: data
                    }
                  }
                )
                let pages = getCurrentPages()
          let prepage = pages[pages.length - 2]
          let act = currentTab == 0 ? prepage.data.actNow : prepage.data.actFinished
          act[id].applystatus = 0
          currentTab == 0 ? prepage.setData({
            actNow: act
          }) : prepage.setData({
            actFinished: act
          })
          wx.showModal({
            title: '感谢您的推荐',
            content: '一经采纳，您的推荐即将成为系统线路',
            showCancel: false,
            success: (res) => {
              wx.navigateBack()
            }
          })

              }
            })
          }
        }
      })

      .catch((err) => {
        console.log(err)
      })
 

  },
  onLoad: function(options) {
    aid = options.aid
    currentTab = options.currentTab
    id = options.id
  },

})