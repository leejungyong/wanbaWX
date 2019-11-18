Page({

  /** 
   * 页面的初始数据 
   */
  data: {
    list: null,
    marker: null,   //地图上标记的点 
    lat: null,
    lng: null,
  },

  fetchData(list) {
    let tmp = []
    for (let i in list) {
      let latlng = list[i].latlng.split(',')
      let lat = latlng[0]
      let lng = latlng[1]
      let marker = {
        'id': i,
        'alpha': 0.8,
        'latitude': lat,
        'longitude': lng,
        'iconPath': 'http://img.wondfun.com/wanba/img/site2.png',
        'width': 32,
        'height': 32,
        label: {
          anchorX: 10,
          anchorY: -20,
          color: '#f00',
          fontSize: 16,
          content: list[i].name
        }
      }

      tmp.push(marker)
    }
    console.log(tmp)
    this.setData({
      lat: tmp[0].latitude,
      lng: tmp[0].longitude,
      marker: tmp,
      list: list
    })
    this.includePoints()
  },

  /** 
   * 定位到我的位置 
   */
  toMyPos() {
    let that = this
    var x, y
    wx.getLocation({
      type: 'gcj02',
      success: function (res) {
        const y = res.latitude.toFixed(6)
        const x = res.longitude.toFixed(6)

        that.setData({
          lat: y,
          lng: x
        })
      },
    })

  },
  /** 
   * 包含所有的点 
   */
  includePoints() {
    let that = this
    that.mapCtx.includePoints({
      padding: [100],
      points: that.data.marker
    })
  },

  /** 
   * 选择某一个点 
   */
  chooseMaker(e) {
    // console.log(e) 
    let id = e.markerId
    let poiInfo = this.data.list[id]
    console.log(poiInfo)

    let pages = getCurrentPages()
    let prepage = pages[pages.length - 3]
    let olddata = prepage.data.poiInfo
    olddata.latlng = poiInfo.latlng

    prepage.setData({
      name: poiInfo.name,
      pmemo: poiInfo.pmemo,
      poi: poiInfo.latlng,
      poiInfo: olddata
    })

    wx.navigateBack({
      delta: 2
    })

  },

  /** 
   * 生命周期函数--监听页面加载 
   */
  onLoad: function (options) {
    let list = JSON.parse(options.ops)
    console.log(list)
    this.mapCtx = wx.createMapContext('myMap')
    this.fetchData(list)

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