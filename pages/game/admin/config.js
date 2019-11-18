var aid = null
const app = getApp()
Page({


  data: {
    pvalue: null,
    pvalue1: null,
    pvalue2: null,
    pvalue3: null,
    gps: null,
    mineNum: null,
    mineMoney: null,
    endTime: '选择时间',
    items: [
    {
      name: '管理员手动结算',
      value: 0
    },
    {
      name: '玩家自主结算',
      value: 1
    }
    ],
    linktype:null
  },
  clearEndTime() {
    this.setData({
      endTime: '选择时间'
    })
  },
  bindTimeChange: function(e) {
    var date = new Date();
    var timestamp1 = parseInt(date.getTime() / 1000);
    console.log(timestamp1)
    var seperator1 = "-";
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var strDate = date.getDate();
    if (month >= 1 && month <= 9) {
      month = "0" + month;
    }
    if (strDate >= 0 && strDate <= 9) {
      strDate = "0" + strDate;
    }
    var currentdate = year + seperator1 + month + seperator1 + strDate;
    var currenttime = currentdate + ' ' + e.detail.value
    currenttime = currenttime.substring(0, 19);
    currenttime = currenttime.replace(/-/g, '/');
    var timestamp2 = parseInt(new Date(currenttime).getTime() / 1000);

    timestamp2 = timestamp2 < timestamp1 ? timestamp2 + 24 * 60 * 60 : timestamp2
    console.log(timestamp2)
    var d = new Date(timestamp2 * 1000); //根据时间戳生成的时间对象
    seperator1 = "-";
    year = d.getFullYear();
    month = d.getMonth() + 1;
    strDate = d.getDate();
    if (month >= 1 && month <= 9) {
      month = "0" + month;
    }
    if (strDate >= 0 && strDate <= 9) {
      strDate = "0" + strDate;
    }
    currentdate = year + seperator1 + month + seperator1 + strDate;
    this.setData({
      endTime: currentdate + ' ' + e.detail.value
    })
  },
  onLoad: function(options) {
    aid = options.aid
    this.fetch(aid)
  },

  back() {
    wx.navigateBack()
  },
  fetch(aid) {
  wx.showLoading({
      title: '加载中',
    })
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=actSetting',
      data: {
        aid: aid,
        openid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        wx.hideLoading()

        var gps
        // if (data.act.gpsEnabled == 1) {
        //   gps = data.act.offset
        // } else {
        //   gps = 0
        // }
        gps = data.act.offset
        let linktype=data.act.linktype
        let items=this.data.items
        for (let i in items) {
          if (items[i].value == linktype) {
            items[i].checked = true
          }

        }
        this.setData({
          pvalue: data.act.pvalue,
          pvalue1: data.act.pvalue1,
          pvalue2: data.act.pvalue2,
          pvalue3: data.act.pvalue3,
          gps: gps,
          items:items,
          linktype:linktype,
          mineNum: data.act.minenum,
          mineMoney: data.act.minevalue,
          endTime: data.act.endTime == 0 ? '选择时间' : data.act.endTime
        })
        //console.log(this.data.lands)
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
  radioChange(e) {
    let v = e.detail.value
    this.setData({
      linktype:v
    })
  },
  updatePvalue: function(e) {
    let pvalue = e.detail.value
    console.log(pvalue)
    this.setData({
      pvalue: pvalue
    })

  },
  updatePvalue1: function(e) {
    let pvalue = e.detail.value
    console.log(pvalue)
    this.setData({
      pvalue1: pvalue
    })

  },
  updatePvalue2: function(e) {
    let pvalue = e.detail.value
    console.log(pvalue)
    this.setData({
      pvalue2: pvalue
    })

  },
  updatePvalue3: function(e) {
    let pvalue = e.detail.value
    console.log(pvalue)
    this.setData({
      pvalue3: pvalue
    })

  },
  updateGPS(e) {
    let gps = e.detail.value
    this.setData({
      gps: gps
    })
  },
  updateMineNum(e) {
    let mineNum = e.detail.value
    this.setData({
      mineNum: mineNum
    })
  },
  updateMineMoney(e) {
    let mineMoney = e.detail.value
    this.setData({
      mineMoney: mineMoney
    })
  },
  confirm() {
    let that = this
    let pvalue = this.data.pvalue
    if (pvalue == '' || isNaN(parseInt(pvalue)) || parseInt(pvalue) <= 0) {
      wx.showToast({
        title: '请设置普通点预设地价',
        icon: 'none'
      })
      return
    }
    let pvalue1 = this.data.pvalue1
    if (pvalue1 == '' || isNaN(parseInt(pvalue1)) || parseInt(pvalue1) <= 0) {
      wx.showToast({
        title: '请设置拍卖点预设地价',
        icon: 'none'
      })
      return
    }
    let pvalue2 = this.data.pvalue2
    if (pvalue2== '' || isNaN(parseInt(pvalue2)) || parseInt(pvalue2) <= 0) {
      wx.showToast({
        title: '请设置G点预设地价',
        icon: 'none'
      })
      return
    }
    let pvalue3 = this.data.pvalue3
    if (pvalue3 == '' || isNaN(parseInt(pvalue3)) || parseInt(pvalue3) <= 0) {
      wx.showToast({
        title: '请设置挑战点预设地价',
        icon: 'none'
      })
      return
    }
    let gps = parseInt(this.data.gps)
    console.log(gps)
    console.log(isNaN(gps))
    if (isNaN(gps) || gps < 0) {
      wx.showToast({
        title: '请设置GPS',
        icon: 'none'
      })
      return
    }
    let mineNum = this.data.mineNum
    if (mineNum == '' || isNaN(parseInt(mineNum)) || parseInt(mineNum) <= 0) {
      wx.showToast({
        title: '请设置布雷数量限制',
        icon: 'none'
      })
      return
    }
    let mineMoney = this.data.mineMoney
    if (mineMoney == '' || isNaN(parseInt(mineMoney)) || parseInt(mineMoney) <= 0) {
      wx.showToast({
        title: '请设置布雷金额限制',
        icon: 'none'
      })
      return
    }
    let endTime = this.data.endTime == '选择时间' ? '' : this.data.endTime
    let ops = {
      aid: aid,
      pvalue: pvalue,
      pvalue1: pvalue1,
      pvalue2: pvalue2,
      pvalue3: pvalue3,
      gps: gps,
      mineNum: mineNum,
      mineMoney: mineMoney,
      endTime: endTime,
      linktype:that.data.linktype
    }
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=postSetting',
      data: ops,
      method: 'POST',
      success: (res) => {
        let data = res.data
        console.log(data)
        wx.showToast({
          title: data.msg,
        })
        setTimeout(() => {
          wx.navigateBack()
        }, 2000)
      },
      fail: (res) => {

      }
    })
  },
})