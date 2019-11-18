const app = getApp()
var cat=null
Page({

  data: {
    marker: null,
    lat: 30.266482,
    lng: 120.11,
    address: '',
    disLat: 30.266482,
    disLng: 120.11,
   
  },
  toMypos(){
    let that = this
    var x, y
    var key = app.globalData.config.mapSubkey



    wx.getLocation({
      type: 'gcj02',
      success(res) {
        const y = res.latitude.toFixed(6)

        const x = res.longitude.toFixed(6)
        wx.request({
          url: 'https://apis.map.qq.com/ws/geocoder/v1/?location=' + y + ',' + x + '&key=' + key,
          success: (res) => {

            let dz = res.data.result.address
            console.log(dz)
            
            that.setData({
              lat: y,
              lng: x,
              disLng: x,
              disLat: y,
              address: dz
            })
          }
        })
      }
    })
  },
  save() {
  
    
      let ops = {
        latlng: this.data.lat + ',' + this.data.lng,
        address: this.data.address,
        name: '',
        pmemo: '',
        cat : cat
      }
      wx.navigateTo({
        url: './poiSetting?ops=' + JSON.stringify(ops),
      })

   
  },

  getAddress(lat, lng) {
    let that = this
    let poi = lat + ',' + lng
    let key = app.globalData.config.mapSubkey
    wx.request({
      url: 'https://apis.map.qq.com/ws/geocoder/v1/?location=' + poi + '&key=' + key,

      success: (res) => {
        let result = res.data.result
        console.log(res.data)
        that.setData({
          lat: result.location.lat,
          lng: result.location.lng,
          disLat: result.location.lat.toFixed(6),
          disLng: result.location.lng.toFixed(6),
          address: result.address
        })
      },
      fail: (err) => {
        console.log(err)
      }
    })
  },

  regionChange(e) {
    //console.log(e)
    // 地图发生变化的时候，获取中间点，也就是用户选择的位置toFixed
    if (e.type == 'end' && (e.causedBy == 'scale' || e.causedBy == 'drag')) {
      // console.log(e)
      var that = this;

      this.mapCtx.getCenterLocation({
        type: 'gcj02',
        success: function(res) {
          //console.log(res)
          that.getAddress(res.latitude, res.longitude)
          
        }
      })

    }

  },


  onLoad: function(options) {
    
    cat = options.cat ? options.cat :''
      wx.setNavigationBarTitle({
        title: '未命名点位'
      })

    this.mapCtx = wx.createMapContext('myMap')
    this.toMypos()
  },
  verifylonglat(latitude, longitude) { 
    var longreg = /^(\-|\+)?(((\d|[1-9]\d|1[0-7]\d|0{1,3})\.\d{0,6})|(\d|[1-9]\d|1[0-7]\d|0{1,3})|180\.0{0,6}|180)$/; 
    if (!longreg.test(longitude)) {  
      return false; 
    } 
    //纬度,整数部分为0-90小数部分为0到6位
     
    var latreg = /^(\-|\+)?([0-8]?\d{1}\.\d{0,6}|90\.0{0,6}|[0-8]?\d{1}|90)$/; 
    if (!latreg.test(latitude)) {  
      return false; 
    } 
    return true;
  }
})