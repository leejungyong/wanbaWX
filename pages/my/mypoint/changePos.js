const app = getApp()
Page({

  data: {
    marker: null,
    lat: 30.266482,
    lng: 120.11,
    address: '',
    disLat: 30.266482,
    disLng: 120.11
  },
  toMypos() {
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
    let pages = getCurrentPages();
    let prepage = pages[pages.length - 2]
    let poiInfo = prepage.data.poiInfo
    poiInfo.latlng = this.data.lat + ',' + this.data.lng
    let address = this.data.address
    let poi = this.data.lat + ',' + this.data.lng
    prepage.setData({
      poiInfo: poiInfo,
      poi: poi,
      address:address
    })
    wx.showToast({
      title: '点位信息已保存更新',
      icon: 'none'
    })
    setTimeout(() => {
      wx.navigateBack()
    }, 2000)
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
        success: function (res) {
          //console.log(res)
          that.getAddress(res.latitude, res.longitude)
          // that.setData({
          //   lat: res.latitude,
          //   lng: res.longitude,
          //   marker: [{
          //     'id': 1,
          //     'alpha': 0.8,
          //     'latitude': res.latitude,
          //     'longitude': res.longitude,

          //   }]
          // })
        }
      })

    }

  },


  onLoad: function (options) {
    let that = this
    let ops = JSON.parse(options.ops)
    console.log(ops)
    let name = ops.name == '' ? '未命名点位' : ops.name
    wx.setNavigationBarTitle({
      title: name
    })
    let lat = that.data.lat
    let lng = that.data.lng
    // 小程序与后端情求接口
    var x, y
    var key = app.globalData.config.mapSubkey
    var req = function (obj) {
      return new Promise(function (resolve, reject) {

        wx.request({

          url: obj.url,

          data: obj.data,

          header: obj.header,

          method: obj.method == undefined ? "get" : obj.method,

          success: function (data) {
            // 回调成功执行resolve 
            resolve(data)

          },

          fail: function (data) {            // 回调失败时

            if (typeof reject == 'function') {

              reject(data);

            } else {

              console.log(data);

            }

          },

        })

      })

    }
    if (ops.latlng.length > 0 && ops.latlng.indexOf(',') > -1) {
      let latlng = ops.latlng.split(',')
      if (latlng.length == 2) {
        let s = that.verifylonglat(latlng[0], latlng[1])
        if (s) {
          // lat = latlng[0]
          // lng = latlng[1]
          y = latlng[0]
          x = latlng[1]
          let req1 = req({

            url: 'https://apis.map.qq.com/ws/geocoder/v1/?location=' + ops.latlng + '&key=' + key,

            data: {},

          })
          req1.then(function (data) {

            console.log(data)
            let dz = data.data.result.address
            console.log(dz)
            let marker = {
              'id': 1,
              'alpha': 0.8,
              'latitude': y,
              'longitude': x,

            }
            let temp = []
            temp.push(marker)
            that.setData({
              lat: y,
              lng: x,
              marker: temp,
              disLng: x,
              disLat: y,
              address: dz
            })


          })
            .catch(function (err) {
              console.log(err)
            })
        }
      }

    } else if (ops.poi.length > 0 && ops.poi.indexOf(',') > -1) {

      let latlng = ops.poi.split(',')
      if (latlng.length == 2) {
        let s = that.verifylonglat(latlng[0], latlng[1])
        if (s) {

          // 执行req 方法,传入第一个请求,

          let mapApi = 'https://apis.map.qq.com/ws/coord/v1/translate?locations=' + ops.poi + '&type=1&key=' + key;
          let req1 = req({

            url: mapApi,

            data: {},

          })
          // 当需要多次请求时加入

          req1.then(function (data) {

            console.log('promiseThen1')

            console.log(data);
            y = data.data.locations[0].lat
            x = data.data.locations[0].lng
            let latlng = data.data.locations[0].lat + ',' + data.data.locations[0].lng
            return req({

              url: 'https://apis.map.qq.com/ws/geocoder/v1/?location=' + latlng + '&key=' + key,

            })

          })
            .then(function (data) {
              console.log(data)
              let dz = data.data.result.address
              console.log(dz)
              let marker = {
                'id': 1,
                'alpha': 0.8,
                'latitude': y,
                'longitude': x,

              }
              let temp = []
              temp.push(marker)
              that.setData({
                lat: y,
                lng: x,
                marker: temp,
                disLng: x,
                disLat: y,
                address: dz
              })
            })
            .catch(function (data) {

              console.log(data)

            })

        }
      }
    } else {
      wx.getLocation({
        type: 'gcj02',
        success(res) {
          const y = res.latitude
          console.log(lat)
          const x = res.longitude
          wx.request({
            url: 'https://apis.map.qq.com/ws/geocoder/v1/?location=' + y + ',' + x + '&key=' + key,
            success: (res) => {

              let dz = res.data.result.address
              console.log(dz)
              let marker = {
                'id': 1,
                'alpha': 0.8,
                'latitude': y,
                'longitude': x,

              }
              let temp = []
              temp.push(marker)
              that.setData({
                lat: y,
                lng: x,
                marker: temp,
                disLng: x,
                disLat: y,
                address: dz
              })
            }
          })
        }
      })
    }



    // that.getAddress(lat, lng)
    console.log(this.data)
    this.mapCtx = wx.createMapContext('myMap')
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