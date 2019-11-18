const WxParse = require('../../wxParse/wxParse.js')
const app = getApp()
Page({

  data: {
    navbar: ['简介', '玩法'],
    imgwidth: 0,
    imgheight: 0,
    currentTab: 0,
    content:null
  },
  
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
  fetch(aid,to) {
    wx.showLoading({
      title: '加载中',
    })
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=fetchContent',
      data: {
        aid:aid,
        to:to
      },
      method: 'POST',
      success: (res) => {
        let ds = res.data
        //console.log(ds)
        let memo1 = ds.content.content
        // memo1.replace(/<p[^>/]*>/g, '<p>')
        // memo1.replace(/<span[^>/]*>/g, '<span>')
        // console.log(memo1)
        memo1 = memo1.replace(/<strong(([\s\S])*?)>/g, "<span>");
        memo1 = memo1.replace(/<section(([\s\S])*?)>/g, "<span>");
        memo1=memo1.replace(/<span(([\s\S])*?)>/g, "<span>");
        memo1=memo1.replace(/<p(([\s\S])*?)>/g,  "<p>");
       // console.log(memo1)
        WxParse.wxParse('article', 'html', memo1, that, 5);
        let memo2 = ds.summary.content
        memo2 = memo2.replace(/<strong(([\s\S])*?)>/g, "<span>");
        memo2 = memo2.replace(/<section(([\s\S])*?)>/g, "<span>");
        memo2 = memo2.replace(/<span(([\s\S])*?)>/g, "<span>");
        memo2 = memo2.replace(/<p(([\s\S])*?)>/g, "<p>");
        WxParse.wxParse('summary', 'html', memo2, that, 5);
        let content = {
          title: ds.content.title,
          pic: ds.content.pic
        }
        that.setData({
          aid:aid,
          content: content
        })
        wx.setNavigationBarTitle({
          title: ds.content.title
        })
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

  onLoad: function(options) {
    let aid=options.aid
    let to = options.to
    console.log()
    let that = this
    that.fetch(aid,to)
    }

    
})