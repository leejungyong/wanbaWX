const app = getApp()
var aid = 0
Page({

  data: {
    qarr: ['人工判定', '回答精确匹配答案', '回答模糊匹配答案', '答案包含在回答里', '回答包含在答案里', '教练提交管理员判定', 'N选3'],
    qarr1: ['教练打分', '回答精确匹配答案', '回答模糊匹配答案', '答案包含在回答里', '回答包含在答案里', '教练提交管理员判定', 'N选3'],
    index: 0,
    cat: null,
    qtype: '',
    poiInfo: null,
    name: '',
    pmemo: '',
    ptype: null,
    pvalue: null,
    tip1: null,
    tip2: null,
    poi: '',
    memo: '',
    answer: '',
    btnTxt: '播放音频',
    openSetting: [{
        name: '开放',
        value: 0
      },
      {
        name: '未开放',
        value: 1
      }
    ],
    posSetting: [{
        name: '普通点',
        value: 0
      },
      {
        name: '拍卖点',
        value: 1
      },
      {
        name: 'G点',
        value: 2
      },
      {
        name: '挑战点',
        value: 3
      }
    ],
    gpsSetting: [{
        name: '关',
        value: 0
      },
      {
        name: '开',
        value: 1
      }
    ],
  },
  bindPickerChange(e) {
    let index = e.detail.value
    console.log(index)
    if (index == 0 || index == 5) {
      if(this.data.ptype==1){
        this.setData({
          index: index,
          qtype: this.data.qarr[index],
          answer: ''
        })
      }
      else{
        this.setData({
          index: index,
          qtype: this.data.qarr1[index],
          answer: ''
        })
      }
      
    } else {
      this.setData({
        index: index,
        qtype: this.data.qarr[index],
      })
    }

  },
  onLoad: function(options) {

    let that = this
    let cat = options.cat
    let data = JSON.parse(options.data)
    aid = options.aid
    console.log(data)
    wx.setNavigationBarTitle({
      title: data.displayorder + '号点位设置',
    })

    let open = this.data.openSetting
    for (let i in open) {
      if (open[i].value == data.open) {
        open[i].checked = true
      }
    }
    let pos = this.data.posSetting
    for (let i in pos) {
      if (pos[i].value == data.ptype) {
        pos[i].checked = true
      }
    }
    let gps = this.data.gpsSetting
    for (let i in gps) {
      if (gps[i].value == data.gps) {
        gps[i].checked = true
      }
    }
    if (data.media == 1) {
      this.registerAudioContext(data.url);
    }
    let index = 0
    if (data.ptype == 2) {
      index = 5
    } else if (data.ptype == 3) {
      index = data.qtype == 7 ? 0 : data.qtype
    } else {
      index = data.qtype
    }
    let qtype = ''
    if (data.ptype == 2) {
      qtype = that.data.qarr[5]
    } else if (data.ptype == 3) {
      qtype = data.qtype == 7 ? '教练打分' : that.data.qarr[data.qtype]
    } else {
      qtype = that.data.qarr[data.qtype]
    }
    this.setData({
      cat: cat,
      openSetting: open,
      gpsSetting: gps,
      posSetting: pos,
      poiInfo: data,
      name: data.name,
      pmemo: data.pmemo,
      memo: data.memo,
      answer: data.answer,
      ptype: data.ptype,
      pvalue: data.pvalue,
      tip1: data.tip1,
      tip2: data.tip2,
      index: index,
      qtype: qtype,
      poi: data.latlng ? data.latlng : data.poi
    })
  },
  onUnload() {
    if (this.data.poiInfo.media == 1) {
      this.innerAudioContext.stop()
    }
  },
  play() {

    if (this.data.btnTxt == '播放音频' || this.data.btnTxt == '继续播放') {

      this.innerAudioContext.play()
      this.setData({
        btnTxt: '暂停播放'
      })
    } else {

      this.innerAudioContext.pause()
      this.setData({
        btnTxt: '继续播放'
      })
    }

  },
  registerAudioContext: function(src) {
    //console.log('ok')
    this.innerAudioContext = wx.createInnerAudioContext();
    this.innerAudioContext.src = src
    this.innerAudioContext.onEnded(
      (res) => {
        this.setData({
          btnTxt: '播放音频'
        })
      })
    this.innerAudioContext.onError((res) => {
      console.log('播放音频失败' + res);
    })
    this.innerAudioContext.onStop((res) => {
      console.log('播放结束!');
    })
  },
  updateOpenSetting(e) {
    let v = e.detail.value
    let poiInfo = this.data.poiInfo
    poiInfo.open = v
    this.setData({
      poiInfo: poiInfo
    })
  },
  updatePosSetting(e) {
    let that = this
    let v = e.detail.value
    let poiInfo = this.data.poiInfo
    var qtype, index
    if (v == 3) {
      index = 0
      qtype = that.data.qarr1[index]
    } else if (v == 2) {
      index = 5
      qtype = that.data.qarr[index]
    } else {
      index = 0
      qtype = that.data.qarr[index]
    }
    this.setData({
      poiInfo: poiInfo,
      ptype: v,
      qtype: qtype,
      index: index,
      pvalue: v == 1 ? 300 : that.data.pvalue
    })
    //console.log(this.data)
  },
  updateGpsSetting(e) {
    let v = e.detail.value
    let poiInfo = this.data.poiInfo
    poiInfo.gps = v
    this.setData({
      poiInfo: poiInfo
    })

  },
  updateName(e) {
    this.setData({
      name: e.detail.value
    })
  },
  updatePmemo(e) {
    this.setData({
      pmemo: e.detail.value
    })
  },
  updatePvalue(e) {
    this.setData({
      pvalue: e.detail.value
    })
  },
  updateMemo(e) {
    this.setData({
      memo: e.detail.value
    })
  },
  updateTip1(e) {
    this.setData({
      tip1: e.detail.value
    })
  },
  updateTip2(e) {
    this.setData({
      tip2: e.detail.value
    })
  },
  updateAnswer(e) {
    this.setData({
      answer: e.detail.value
    })
  },
  taskList() {

    wx.navigateTo({
      url: './taskList',
    })
  },
  preview(e) {
    let id = e.currentTarget.id
    let pics = this.data.poiInfo.pics.map((item) => {
      return item.url
    })
    wx.previewImage({
      current: pics[id],
      urls: pics
    })
  },
  importPos() {
    wx.navigateTo({
      url: './importPos',
    })
  },
  save() {
    let that = this
    if (that.data.name == '' || that.data.poi == '' || that.data.memo == '') {
      wx.showToast({
        title: '星号*为必填项',
        icon: 'none'
      })
      return
    }
    if ((that.data.index != 0 && that.data.index != 5) && that.data.answer == '') {
      wx.showToast({
        title: '请设置答案',
        icon: 'none'
      })
      return false
    }
    let tk = new Date().getTime();
    let cache = wx.getStorageSync('lastpost')
    wx.setStorageSync('lastpost', tk)

    if (cache) {

      let duration = tk - cache
      if (duration < 3000) {
        wx.showToast({
          title: '手速有点过快呀，休息下，过几秒再点击吧',
          icon: 'none'
        })
        return
      } else {
        new Promise(that.postData).then(function(data) {
          //console.log(data)
          if (data.status) {
            wx.showToast({
              title: data.msg,
              icon: 'none'
            })
            let pages = getCurrentPages()
            let prepage = pages[pages.length - 2]
            let task = data.data
            console.log(task)
            let index = task.displayorder - 1
            let lands = prepage.data.lands
            //console.log(lands)
            lands[index] = task
            prepage.setData({
              lands: lands
            })

            setTimeout(() => {
              wx.navigateBack()
            }, 2000)
          } else {
            wx.showToast({
              title: data.msg,
              icon: 'none'
            })
          }

        }).catch(function(reason) {
          wx.showToast({
            title: reason,
            icon: 'none'
          })
        });
      }

    }

  },

  //位置定位
  selectPos() {
    let poiInfo = this.data.poiInfo
    let name = this.data.name
    //poiInfo.title=name
    let ops = {
      name: name,
      poi: poiInfo.poi,
      latlng: poiInfo.latlng
    }
    //console.log(ops)
    wx.navigateTo({
      url: './selectPos?ops=' + JSON.stringify(ops),
    })
  },
  postData(resolve, reject) {
    let that = this
    let poiInfo = that.data.poiInfo
    poiInfo.name = that.data.name
    poiInfo.pmemo = that.data.pmemo
    poiInfo.pvalue = that.data.pvalue
    poiInfo.ptype = that.data.ptype
    poiInfo.answer = that.data.answer
    poiInfo.memo = that.data.memo
    poiInfo.tip1 = that.data.tip1
    poiInfo.tip2 = that.data.tip2
    if (poiInfo.ptype == 2) {
      poiInfo.qtype = 5
    } else if (poiInfo.ptype == 3) {
      poiInfo.qtype = that.data.index == 0 ? 7 : that.data.index
    } else {
      poiInfo.qtype = that.data.index
    }
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=savePoi',
      data: {
        aid: aid,
        poiInfo: poiInfo,
        openid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: (res) => {
        console.log(res)
        let data = res.data
        resolve(data);

      },
      fail: (err) => {
        reject('网络错误');
      }
    })
  }
})