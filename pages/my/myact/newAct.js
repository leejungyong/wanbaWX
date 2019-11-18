var newaid=null
const app = getApp()
Page({


  data: {
    imgUrl: app.globalData.config.imgUrl,
    title: '',
    date: '请选择活动时间',
    teamNum: 6,
    maxNum: 6,
    pic: null,
    cat: 0,
    cats: [{
      name: '团建模式',
      value: 0,
      checked:true
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
    themeName:'十二神兽'
  },
  //跳转至主页面
  toTheme(){
    wx.navigateTo({
      url: './sysTheme',
    })
  },
  sliderchange(e) {
    this.setData({
      teamNum: e.detail.value
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
  radioChange(e) {
    let v = e.detail.value
    console.log(v)
    this.setData({
      cat: v
    })


  },
  
  next() {
    var aid
    let that = this
    let status = that.beforePost()
    if (status) {
      var req = function (obj) {
        return new Promise(function (resolve, reject) {

          wx.request({

            url: obj.url,

            data: obj.data,

            header: obj.header,

            method: obj.method == undefined ? "get" : obj.method,

            success: function (data) {
              resolve(data)

            },

            fail: function (data) {

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
        url: app.globalData.config.apiUrl + 'index.php?act=newAct',
        data: {
          actdata: that.data,
          openid: wx.getStorageSync('openid')
        },
        method: 'POST',
      })
      req1.then((res) => {
        let data = res.data
        let pic = that.data.pic
        aid = data.aid
        if (data.status && data.aid && pic) {
         aid  = data.aid
         
          let pages = getCurrentPages()
          let prePage = pages[pages.length - 2]
          let list = data.list
          prePage.setData({
            actNow: list
          })
          if (pic.indexOf('http://tmp/') > -1 || pic.indexOf('wxfile://') > -1) {
            wx.uploadFile({
              url: app.globalData.config.apiUrl + 'uploadactpic.php',
              filePath: pic,
              name: 'file',
              formData: {
                'aid': aid,
                'openid': wx.getStorageSync('openid')
              },
              success: function (res) {
                let data = res.data
                console.log(data)
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
      })
        .then((res) => {
          //console.log(res)
          let pages = getCurrentPages()
          let prepage = pages[pages.length - 2]
          prepage.setData({
            init: true,
            page: 0
          })
          prepage.fetch()
          wx.showToast({
            title: '创建活动成功,请继续进行活动详细设置',
            icon: 'none'
          })
          setTimeout(() => {
            wx.redirectTo({
              url: '../../game/admin/setting?aid=' + aid,
            })
          }, 2000)
        })
        .catch((err) => {
          console.log(err)
          wx.showToast({
            title: '操作出错，请重试',
            icon: 'none'
          })
        })
    }
  },
  save() {
    let that = this
    let status = that.beforePost()
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
      console.log(that.data)
      let req1 = new req({
        url: app.globalData.config.apiUrl + 'index.php?act=newAct',
        data: {
          actdata: that.data,
          openid: wx.getStorageSync('openid')
        },
        method: 'POST',
      })
      req1.then((res)=>{
        let data=res.data
        let pic = that.data.pic
         if(data.status && data.aid && pic){
           var aid=data.aid
           let pages = getCurrentPages()
           let prePage = pages[pages.length - 2]
           let list = data.list
           prePage.setData({
             actNow: list
           })
           if (pic.indexOf('http://tmp/') > -1 || pic.indexOf('wxfile://') > -1){
             wx.uploadFile({
               url: app.globalData.config.apiUrl + 'uploadactpic.php',
               filePath: pic,
               name: 'file',
               formData: {
                 'aid': aid,
                 'openid': wx.getStorageSync('openid')
               },
               success: function (res) {
                 let data = res.data
                 console.log(data)
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
      })
      .then((res)=>{
        //console.log(res)
        let pages=getCurrentPages()
        let prepage = pages[pages.length - 2]
        prepage.setData({
          init:true,
          page:0
        })
        prepage.fetch()
        wx.showToast({
          title: '创建活动成功',
          icon: 'none'
        })
        setTimeout(() => {
          wx.navigateBack()
        }, 2000)
      })
      .catch((err)=>{
         console.log(err)
        wx.showToast({
          title: '操作出错，请重试',
          icon: 'none'
        })
      })
    }
  },
  delPic(){
    this.setData({
      pic:null
    })
  },
  preview(){
    let pics=[]
    pics.push(this.data.pic)
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


        pic = res.tempFilePaths[0]

        that.setData({
          pic: pic
        })
        console.log(that.data.pic)
      }
    })
  },
  onLoad: function(options) {

  }

})