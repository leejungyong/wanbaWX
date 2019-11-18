var aid, act_title, actStatus = null
const app = getApp()
Page({


  _data: {
    link_type:null,
    teamid: 0,
    modes: [{
        name: '一次测试',
        value: 0
      },
      {
        name: '二次测试',
        value: 1
      },
      {
        name: '试玩模式',
        value: -1
      },
      {
        name: '正式模式',
        value: 2
      }
    ],

    items: [{
        name: '未开放',
        value: -1
      },
      {
        name: '游戏中',
        value: 0
      },
      {
        name: '游戏结束',
        value: 1
      },
      {
        name: '交易中',
        value: 2
      },
      {
        name: '交易结束',
        value: 3
      },
      {
        name: '拍卖中',
        value: 4
      },
      {
        name: '结算阶段',
        value: 5
      },
      {
        name: '全场结束',
        value: 6
      }
    ],
  },

  data: {
    lands: null,
    teams: null,
    items: null,
    modes: null,
    currentTab: -1,

    openArea: [{
        name: '上部',
        value: 1
       
      },
      {
        name: '右部',
        value: 2
      
      },
      {
        name: '下部',
        value: 3
      
      },
      {
        name: '左部',
        value: 4
       
      },
    ],
  },
  onLoad: function(options) {
    aid = options.aid
    this.fetch()
  },
  navbarTap(e) {
    let that = this
    let idx = e.currentTarget.dataset.idx
    //  console.log(idx)
    if (idx == 3 && that.data.currentTab < 3) {
      wx.showModal({
        title: '警告',
        content: '确定要结束测试，切换为正式模式吗？所有测试数据将被清空，活动状态将设置为未开放。',
        success(res) {

          if (res.confirm) {
            var v
            if(idx==3){
              v=2
            }else if(idx==2){
              v=-1
            }else{
              v=idx
            }
            wx.request({
              url: app.globalData.config.apiUrl + 'index.php?act=updateActModeTest',
              data: {
                aid: aid,
                mode: v
              },
              method: 'POST',
              success: (res) => {
                let data = res.data
                // console.log(data)
                if (data.status) {
                  that.setData({
                    currentTab: idx,
                    testAgain: false
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
    } else {
      if (that.data.currentTab < 3) {
        that.sysInit(idx)
      }
    }
  },
  auctionScore() {
    if (actStatus < 5) {
      wx.showToast({
        title: '拍卖结束才可以进行结算',
        icon: 'none'
      })
    } else {
      wx.navigateTo({
        url: './auctionscore?aid=' + aid
      })
    }
  },
  viewTeam() {
    wx.navigateTo({
      url: './viewTeam?aid=' + aid
    })
  },
  checkboxChange(e) {
    let v = e.detail.value
    //console.log(v)
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=updateOpenAreaStatus',
      data: {
        aid: aid,
        data: v
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        console.log(data)


      },
      fail: (res) => {
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })

  },

  radioChange(e) {
    let v = e.detail.value
    //console.log(v)
    let that = this

    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=updateActStatus',
      data: {
        aid: aid,
        mode: that.data.currentTab,
        status: v
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        // console.log(data)
        actStatus = v

      },
      fail: (res) => {
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })


  },
  log() {
    wx.navigateTo({
      url: './log?aid=' + aid,
    })
  },
  fetch() {


    wx.showLoading({
      title: '加载中',
    })
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=actInfo',
      data: {
        aid: aid,
        openid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        console.log(data)
        act_title = data.act.title
        let tasks = data.task

        for (let i in tasks) {
          //tasks[i].currentowner = tasks[i].owner 
          let owners = tasks[i].owner ? tasks[i].owner.split(',') : []
          tasks[i].owner = owners

        }

        wx.hideLoading()
        actStatus = data.act.status
        let cat = data.act.cat
        let items = that._data.items
        //在旅行模式下 无交易状态
        if (cat == 1) {
          items.splice(3, 2)
        }
        for (let i in items) {
          if (items[i].value == actStatus) {
            items[i].checked = true
          }

        }
        let openareaData = data.act.openarea.split(',')
        // console.log(openareaData)
        let openArea = that.data.openArea
        console.log(openArea)
        for (let i in openArea) {
          for (let j in openareaData) {
            if (openArea[i].value == openareaData[j]) {
              openArea[i].checked = true
            }
          }
        }
        let mode = data.act.mode
        
        let modes = that._data.modes
        
        
        var ctab
        for (let i in modes) {
          if (modes[i].value == mode) {
            ctab = mode
          }

        }

        if(ctab==2){
          ctab=3
        }else if(ctab==-1){
          ctab=2
        }
        
        this.setData({
          lands: data.task,
          currentTab: ctab,
          teams: data.teams,
          items: items,
          modes: modes,
          openArea: openArea,
          linktype:data.act.linktype
        })

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
  downPhotos() {


    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=downPhotos',
      data: {
        aid: aid,
        openid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        //console.log(data)
        let content = act_title + '照片下载地址:' + data
        wx.setClipboardData({
          data: content,
          success(res) {

            wx.showModal({
              title: '提示',
              showCancel: false,
              content: '照片下载地址已复制到剪贴板',
              success(res) {

              }
            })
          }
        })

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
  hide() {
    this.setData({
      hiddenDownPhoto: true
    })
  },
  copyLink() {
    let that = this
    wx.setClipboardData({
      data: that.data.linkUrl,
      success(res) {

        wx.showToast({
          title: '下载地址已复制',
        })
      }
    })
  },

  onPullDownRefresh: function() {
    wx.showNavigationBarLoading();
    this.fetch()
    wx.hideNavigationBarLoading();
    wx.stopPullDownRefresh()
  },
  sysInit(idx) {
    let that = this
    let v=idx==2 ? -1:idx
    wx.showModal({
      title: '警告',
      content: '此操作会清空所有数据，确定要执行操作吗？',
      success(res) {
        if (res.confirm) {
          wx.request({
            url: app.globalData.config.apiUrl + 'index.php?act=sysInitTest',
            data: {
              aid: aid,
              mode: v
            },
            method: 'POST',
            success: (res) => {
              let data = res.data
              //console.log(data)
              if (data.status) {
                that.setData({
                  currentTab: idx,

                })
                that._data.items[0].checked = true
                wx.showToast({
                  title: data.msg,
                })
                setTimeout(() => {
                  that.fetch()
                }, 2000)
              } else {
                wx.showToast({
                  title: '   出错了',
                })
              }
            },
            fail: (res) => {

            }
          })
        } else if (res.cancel) {
          console.log('用户点击取消')
        }
      }
    })
  }
})