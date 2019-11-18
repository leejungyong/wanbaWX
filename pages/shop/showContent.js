const WxParse = require('../../wxParse/wxParse.js')
var QQMapWX = require('../../utils/qqmap-wx-jssdk.min.js');
var qqmapsdk;
const app = getApp()
var aid,title,sharepic;
Page({

  data: {
    navbar: ['背景故事', '经典玩法','场景介绍','点位信息'],
    imgwidth: 0,
    imgheight: 0,
    currentTab: 0,
    marker: null,
    lat: null,
    lng: null,
    content:null,
    cdn: app.globalData.config.cdn,
    aid:null
  },
  includePoints() {
    let that = this
    that.mapCtx.includePoints({
      padding: [100],
      points: that.data.marker
    })
  } ,
  navbarTap: function(e) {
    this.setData({
      currentTab: e.currentTarget.dataset.idx
    })
  },
  picLoad: function(e) {
    var _this = this;
    var $width = e.detail.width, //获取图片真实宽度
      $height = e.detail.height,
      ratio = $width / $height; //图片的真实宽高比例
    var viewWidth = 640, //设置图片显示宽度，
      viewHeight = 640 / ratio; //计算的高度值   
    this.setData({
      imgwidth: viewWidth,
      imgheight: viewHeight
    })

  },
  routeFav(){
    let that=this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=routeFav',
      data: {
        catid: aid,
        uid: wx.getStorageSync('openid'),
        token: wx.getStorageSync('token'),
        cat:0
      },
      method: 'POST',
      success: (res) => {
        let ds = res.data
        if(ds.status){
          let c=that.data.content
          c.fav=ds.fav
          that.setData({
            content:c
          })
          wx.showToast({
            title: ds.msg,
            mask:true
          })
        }
        
      },
      fail: (err) => {
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },
  buy(){
    let price = this.data.content.price ? this.data.content.price :99
    if(aid==1){
      price=0
    }
    let tid = this.data.content.aid
    wx.navigateTo({
      url: './buyRoute?tid='+tid+'&price='+price,
    })
  },
  fetch(aid) {
    wx.showLoading({
      title: '加载中',
    })
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=getRouteContent',
      data: {
        aid:aid,
        uid:wx.getStorageSync('openid'),
        token:wx.getStorageSync('token')
      },
      method: 'POST',
      success: (res) => {
        let ds = res.data
        console.log(ds)
        let tasks=ds.tasks
        let temp = []
        for (let i in tasks) {
          let latlng = tasks[i].latlng.split(',')
          let lat = latlng[0]
          let lng = latlng[1]

          let marker = {
            'id': i,
            'alpha': 0.8,
            'latitude': lat,
            'longitude': lng,
            label: {
              anchorX: 10,
              anchorY: -20,
              color: '#f00',
              fontSize: 16,
              content: tasks[i].name
            }

          }

          temp.push(marker)

        }
        this.setData({
          content:ds,
          lat: temp[0].latitude,
          lng: temp[0].longitude,
          marker: temp,
          aid:aid
        })
        wx.setNavigationBarTitle({
          title: ds.title
        })
        title=ds.title
        sharepic= ds.sharepic
        WxParse.wxParse('memo1', 'html', ds.route_desc.memo1, that, 5);
        WxParse.wxParse('memo2', 'html', ds.route_desc.memo2, that, 5);
        WxParse.wxParse('memo3', 'html', ds.route_desc.memo3, that, 5);
        wx.hideLoading()
      },
      fail: (err) => {
        wx.hideLoading()
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },
call(){
  wx.makePhoneCall({
    phoneNumber: '13738005036'
  })
},
  showMarker(e) {
    console.log(e.markerId)
    let id = e.markerId
    let poiInfo = this.data.content.tasks[id]
    console.log(poiInfo)
   
  },
  onLoad: function(options) {
    console.log(options)
     aid=options.aid
     console.log(aid)
    this.mapCtx = wx.createMapContext('myMap')
    this.fetch(aid)
    },
  onShareAppMessage: function (ops) {
    let that = this
    //console.log(ops)

    // if (ops.from === 'button') {
      // 来自页面内转发按钮
      //console.log(ops.target)
    // }
    return {
      title: '精选线路——' + title,
      path: 'pages/shop/showContent?aid=' + aid,
      imageUrl: sharepic ? app.globalData.config.apiUrl + 'routepic/' + sharepic : app.globalData.config.apiUrl + 'sharepic/1.jpg',
      success: function (res) {

      },
      fail: function (res) {

      }
    }
  },
  
    
})