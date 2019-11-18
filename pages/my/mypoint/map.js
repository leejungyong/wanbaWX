var QQMapWX = require('../../../utils/qqmap-wx-jssdk.min.js');
var qqmapsdk;

const app = getApp()
Page({

  data: {
    list:null,
    marker: null,
    lat: null,
    lng: null,

  },
  toMypos() {
    let that = this
    var x, y
    wx.getLocation({
      type: 'gcj02',
      success(res) {
        const y = res.latitude.toFixed(6)

        const x = res.longitude.toFixed(6)
        that.setData({
          lat: y,
          lng: x
        })
      }
    })
  },
  editMarker(e){
    console.log(e.markerId)
    let id = e.markerId
    let poiInfo =this.data.list[id]
   // poiInfo.allcats = allcats
    wx.navigateTo({
      url: './editPos?ops=' + JSON.stringify(poiInfo)+'&index=' +id+'&mode=1',
    })
  },
  fetch(list){
    let temp = []
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
          fontSize:16,
          content: list[i].name
        }

      }

      temp.push(marker)

    }
    // console.log(temp)
    this.setData({
      lat: temp[0].latitude,
      lng: temp[0].longitude,
      marker: temp,
      list:list
    })
    this.includePoints()
  },
  onLoad: function (options) {
       let list=JSON.parse(options.ops) 
       console.log(list)
    this.mapCtx = wx.createMapContext('myMap')
      this.fetch(list)
   
  },
includePoints(){
  let that = this
  that.mapCtx.includePoints({
    padding: [100],
    points: that.data.marker
  })
}

})