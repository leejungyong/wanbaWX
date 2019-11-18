var aid,act_title,cat=null
const app = getApp()
Page({

  data: {
    modes: [{
      name: '线下扫码',
      value: 0
    },
    {
      name: '线上AI',
      value: 1
    }
    ],
    currentTab:0,
     stonesleft:null,
     teamStones:null,
    stonesMadehistory:null

  },
  navbarTap(e) {
    let that = this
    let idx = e.currentTarget.dataset.idx
    
      wx.showModal({
        title: '警告',
        content: '确定要切换吗',
        success(res) {

          if (res.confirm) {
            wx.request({
              url: app.globalData.config.apiUrl + 'index.php?act=updateStoneMode',
              data: {
                aid: aid,
                mode: idx
              },
              method: 'POST',
              success: (res) => {
                let data = res.data
                // console.log(data)
                if (data.status) {
                  that.setData({
                    currentTab: idx
                  })
                } else {
                  wx.showToast({
                    title: '操作失败',
                    icon: 'none'
                  })
                }

              },
              fail: (res) => {
                wx.showToast({
                  title: '网络错误',
                  icon: 'none'
                })
              }
            })



          } else {

          }
        }
      })
    
  },
  view(e){
    let id = e.currentTarget.id
    let detail=this.data.teamStones[id].detail
    //console.log(detail)
    if(detail.length>=0){
      wx.navigateTo({
        url: './viewStoneLogUsed?log='+JSON.stringify(detail),
      })
    }
  },
  onLoad: function (options) {
    aid=options.aid
    act_title=options.act_title
    cat=options.cat
    console.log(cat)
    this.fetch()
  },
  stoneSetting() {
    wx.navigateTo({
      url: './stoneSetting?aid=' + aid + '&act_title=' + act_title
    })
  },
  makeStone(){
   wx.navigateTo({
     url: './makeStone?aid='+aid+'&act_title='+act_title+'&cat='+cat,
   })
  },
  fetch() {
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=stoneState',
      data: {
        aid: aid,
        openid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        console.log(data)
        let mode = data.stonemode

        let modes = that.data.modes


        var ctab
        for (let i in modes) {
          if (modes[i].value == mode) {
            ctab = mode
          }

        }
        that.setData({
          currentTab: ctab,
          stonesleft: data.stonesleft,
          teamStones: data.teamStones,
          stonesMadehistory: data.stonesMadehistory
        })

      },
      fail: (res) => {
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }

    })
  },
  downStones() {
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=downStones',
      data: {
        aid: aid,
        openid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        let content = act_title + '宝石下载地址:' + data
        wx.setClipboardData({
          data: content,
          success(res) {

            wx.showModal({
              title: '提示',
              showCancel: false,
              content: '宝石下载地址已复制到剪贴板',
              success(res) {

              }
            })
          }
        })

      },
      fail: (res) => {
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }

    })
  },
})