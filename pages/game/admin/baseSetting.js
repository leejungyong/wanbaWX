var aid = null
const app = getApp()
Page({

  data: {
    imgUrl: app.globalData.config.imgUrl,
    uploadUrl: app.globalData.config.uploadUrl,
    title: '',
    text: '',
    date: '请选择活动时间',
    teamNum: 6,
    maxNum: 6,
    pic: null,
    picLogo: null,
    aid: 0,
    cat:-1,
    cats: [{
      name: '团建模式',
      value: 0
    },
      {
        name: '打卡模式',
        value: 1
      },
      {
        name: '自由模式',
        value: 2
      }],
      themeId:1,
      themeName:''
  },
  sliderchange(e) {
    this.setData({
      teamNum: e.detail.value
    })
  },
  orderTeam(){
    let maxNum = this.data.maxNum
    if(maxNum<12){
     wx.navigateTo({
       url: './orderTeam?aid=' + aid + '&maxNum=' +maxNum 
     })
     }
  },
  toTheme(){
    wx.navigateTo({
      url: '../../my/myact/sysTheme'
    })
  },
  bindDateChange(e) {
    this.setData({
      date: e.detail.value
    })
  },
  updateTitle(e) {
    this.setData({
      title: e.detail.value
    })
  },
  updateText(e) {
    this.setData({
      text: e.detail.value
    })
  },
  beforePost() {
    let that = this
    if (that.data.title == '' || that.data.date == '请选择活动时间') {
      wx.showToast({
        title: '星号*为必填项',
        icon: 'none'
      })
      return false
    }
    let token = new Date().getTime();
    let cache = wx.getStorageSync('lastpost')
    wx.setStorageSync('lastpost', token)
    if (cache) {
      let duration = token - cache
      
      if (duration < 3000) {
        wx.showToast({
          title: '手速有点过快呀，休息下，过几秒再点击吧',
          icon: 'none'
        })
        return false
      }

    }
    return true
  },


  save() {

    let that = this
    console.log(that.data)
    // return
    let status = that.beforePost()
    console.log(status)
    if (status) {
      var req = function(obj) {
        return new Promise(function(resolve, reject) {

          wx.request({

            url: obj.url,

            data: obj.data,

            header: obj.header,

            method: obj.method == undefined ? "get" : obj.method,

            success: function(data) {
              resolve(data)

            },

            fail: function(data) {

              if (typeof reject == 'function') {

                reject(data);

              } else {

                console.log(data);

              }

            },

          })

        })

      }
      let req1 = new req({
        url: app.globalData.config.apiUrl + 'index.php?act=editAct',
        data: {
          actdata: that.data,
          openid: wx.getStorageSync('openid')
        },
        method: 'POST',
      })
      req1.then((res) => {
          let data = res.data
          let pic = that.data.pic
          let picLogo = that.data.picLogo
          console.log(that.data)
          var aid = data.aid
          if (data.status && data.aid && pic) {

            if (pic.indexOf('http://tmp/') > -1 || pic.indexOf('wxfile://') > -1) {
              wx.uploadFile({
                url: app.globalData.config.apiUrl + 'uploadactpic.php',
                filePath: pic,
                name: 'file',
                formData: {
                  'aid': aid,
                  'openid': wx.getStorageSync('openid')
                },
                success: function(res) {
                  let data = res.data
                  // console.log(data)
                  if (data) {
                    return new req({
                      url: app.globalData.config.apiUrl + 'index.php?act=updateActPic',
                      data: {
                        aid: data,
                        openid: wx.getStorageSync('openid')
                      },
                      method: 'POST',
                    })
                  }
                }
              })
            }
          }
          if (data.status && data.aid && picLogo) {
            
            if (picLogo.indexOf('http://tmp/') > -1 || picLogo.indexOf('wxfile://') > -1) {
              console.log('上传logo')
              console.log(picLogo)
              wx.uploadFile({
                url: app.globalData.config.apiUrl + 'uploadlogopic.php',
                filePath: picLogo,
                name: 'file',
                formData: {
                  'aid': aid,
                  'openid': wx.getStorageSync('openid')
                },
                success: function(res) {
                  console.log(res)
                  let data = res.data
                  if (data) {
                    return new req({
                      url: app.globalData.config.apiUrl + 'index.php?act=updateLogoPic',
                      data: {
                        aid: data,
                        openid: wx.getStorageSync('openid')
                      },
                      method: 'POST',
                    })
                  }
                },
                fail(res) {
                  console.log(res)
                }
              })
            }
          }


        })
        .then((res) => {
          console.log(res)

          wx.showToast({
            title: '修改活动成功',
            icon: 'none'
          })
          setTimeout(() => {
            wx.navigateBack()
          }, 2000)
        })
        .catch((err) => {
          // console.log(err)
          wx.showToast({
            title: '操作出错，请重试',
            icon: 'none'
          })
        })
    }
  },
  delPic() {
    this.setData({
      pic: null
    })
  },
  delPicLogo() {
    this.setData({
      picLogo: null
    })
  },
  preview() {
    let pics = []
    pics.push(this.data.pic)
    wx.previewImage({
      urls: pics,
    })
  },
  previewLogo() {
    let pics = []
    pics.push(this.data.picLogo)
    wx.previewImage({
      urls: pics,
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


        pic = res.tempFilePaths[0].toLowerCase()
        if (pic.indexOf('.jpg') == -1) {
          wx.showToast({
            title: '请上传jpg格式图片',
            icon: 'none',
            mask: true
          })

        } else {
          that.setData({
            pic: pic
          })
        }
        // console.log(that.data.pic)
      }
    })
  },
  chooseImgLogo() {
    let that = this
    let picLogo = that.data.picLogo

    wx.chooseImage({
      count: 1,
      sizeType: ['compressed'],
      sourceType: ['album', 'camera'],
      success: function(res) {


        picLogo = res.tempFilePaths[0].toLowerCase()
        if (picLogo.indexOf('.jpg') == -1) {
          wx.showToast({
            title: '请上传jpg格式图片',
            icon: 'none',
            mask: true
          })

        } else {
          that.setData({
            picLogo: picLogo
          })
        }
        // console.log(that.data.picLogo)
      }
    })
  },
  radioChange(e) {
    let v = e.detail.value
    console.log(v)
    this.setData({
      cat:v
    })


  },
  onLoad: function(options) {
    aid = options.aid
    console.log(aid)
    this.fetch()
  },
  fetch() {
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=actBaseInfo',
      data: {
        aid: aid,
       // openid:'oO4Qc5FscWEGitN6E6ZE6cQpqEzk'
        openid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: (res) => {
        console.log(res)
        let data = res.data
        let stamp=new Date().getTime()
        let cats=that.data.cats
        for (let i in cats) {
          if (cats[i].value == data.cat) {
            cats[i].checked = true
          }

        }
        that.setData({
          aid: aid,
          title: data.title,
          date: data.date,
          teamNum: data.teamnum,
          maxNum: data.teamnum > that.data.maxNum ? data.teamnum : that.data.maxNum ,
          pic: data.sharepic ? app.globalData.config.uploadUrl + 'wanba/api/sharepic/' + data.sharepic +'?' +stamp : app.globalData.config.uploadUrl + 'wanba/api/sharepic/1.jpg',
          text: data.slogan,
          cats:cats,
          cat:data.cat,
          picLogo: data.logopic ? app.globalData.config.uploadUrl + 'wanba/api/logopic/' + data.logopic + '?' + stamp: app.globalData.config.uploadUrl + 'wanba/api/logopic/default.jpg',
          themeId:data.teamThemeId,
          themeName:data.themeTitle
        })

        //console.log(that.data)
      },
      fail: (err) => {
        console.log(err)
      }
    })
  }
})