var QQMapWX = require('../../../utils/qqmap-wx-jssdk.min.js');
var qqmapsdk;
var EARTH_RADIUS = 6378137.0;    //单位M
var PI = Math.PI;
const app = getApp()
Page({
  _data: {
    task: null,
    roleid: 0,
    teamid: 0,
    teamname: '',
    act: null,
    myposition: null
  },
  data: {
    marker: null,
    lat: null,
    lng: null,
    subkey: app.globalData.config.mapSubkey

  },

  showDetail: function (e) {
    let that = this
    
      //获取到当前位置
      wx.getLocation({
        type:'gcj02',
        success: function(res) {
          
          let from = {
            latitude: res.latitude,
            longitude: res.longitude
          }
          console.log(from)
          let ops = JSON.stringify(that._data.task)
          ops = ops.replace(/\?/g, '？')
          ops = ops.replace(/\&/g, '＆')
          let act = JSON.stringify(that._data.act)
          
          if (that._data.task.open == 1) {
            wx.showToast({
              title: '此点位尚未开放',
              icon: 'none'
            })
            return
          }
          // if (this._data.roleid > 0) {
          //   wx.showToast({
          //     title: '队长才可以进行此操作',
          //     icon: 'none'
          //   })

          // }
          //如有全局gps限制
          else if (that._data.act.gpsEnabled == 1) {

            let arr = [];
            let center = {
              latitude: that.data.lat,
              longitude: that.data.lng
            }
            arr.push(center)
            qqmapsdk.calculateDistance({
               from: from,
              to: arr,
              success: function (res) {
                console.log(res);
                //console.log(res.result.elements[0].distance)
                let distance = res.result.elements[0].distance
                let offset = that._data.act.offset
                offset = (offset == 0) ? 100 : offset
                if (distance > offset) {
                  wx.showToast({
                    title: '距离目的地' + distance + '米，无法挑战任务',
                    icon: 'none'
                  })
                  return
                } else {
                  if (that._data.roleid == 1) {
                    // console.log(that._data.task.taskid)
                    // console.log(that._data.act.aid)
                    // console.log(that._data.teamid)
                    wx.request({
                      url: app.globalData.config.apiUrl + 'index.php?act=checkPoiRedbagTask',
                      data: {
                        aid: that._data.act.aid,
                        openid: wx.getStorageSync('openid'),
                        taskid: that._data.task.taskid
                      },
                      method: 'POST',
                      success: (res) => {
                        let data = res.data
                        //console.log(data)
                        if (data && data.status == 0) {
                          //console.log('ok')
                          wx.navigateTo({
                            url: './redbagtask?aid=' + that._data.act.aid + '&taskid=' + that._data.task.taskid
                          })
                        } else {

                          wx.navigateTo({
                            url: './task?act=' + act + '&ops=' + ops + '&teamid=' + that._data.teamid + '&teamname=' + that._data.teamname + '&roleid=' + that._data.roleid
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
                  }
                  else {
                    wx.navigateTo({
                      url: './task?act=' + act + '&ops=' + ops + '&teamid=' + that._data.teamid + '&teamname=' + that._data.teamname + '&roleid=' + that._data.roleid
                    })
                  }
                }
              },
              fail: function (res) {
                console.log(res);
                wx.showToast({
                  title: '请在手机系统设置里，允许微信获得定位权限后再尝试，如果仍然失败，请将微信设为信任应用。',
                })
                app.globalData.getLocation = false
                wx.navigateTo({
                  url: '../authlocation',
                })
              },
              complete: function (res) {
                //console.log(res);
              }
            });
          } else {
            let pos_gps = that._data.task.gps
            //虽然全局无限制，但点位有gps限制
            if (pos_gps == 1) {
              let arr = [];
              let center = {
                latitude: that.data.lat,
                longitude: that.data.lng
              }
              arr.push(center)
              qqmapsdk.calculateDistance({
                from:from,
                to: arr,
                success: function (res) {
                  //console.log(res);
                  //console.log(res.result.elements[0].distance)
                  let distance = res.result.elements[0].distance
                  let offset = that._data.act.offset
                  offset = (offset == 0) ? 100 : offset
                  if (distance > offset) {
                    wx.showToast({
                      title: '距离目的地' + distance + '米，无法挑战任务',
                      icon: 'none'
                    })
                    return
                  } else {

                    if (that._data.roleid == 1) {
                      // console.log(that._data.task.taskid)
                      // console.log(that._data.act.aid)
                      // console.log(that._data.teamid)
                      wx.request({
                        url: app.globalData.config.apiUrl + 'index.php?act=checkPoiRedbagTask',
                        data: {
                          aid: that._data.act.aid,
                          openid: wx.getStorageSync('openid'),
                          taskid: that._data.task.taskid
                        },
                        method: 'POST',
                        success: (res) => {
                          let data = res.data
                          //console.log(data)
                          if (data && data.status == 0) {
                            wx.navigateTo({
                              url: './redbagtask?aid=' + that._data.act.aid + '&taskid=' + that._data.task.taskid
                            })
                          } else {

                            wx.navigateTo({
                              url: './task?act=' + act + '&ops=' + ops + '&teamid=' + that._data.teamid + '&teamname=' + that._data.teamname + '&roleid=' + that._data.roleid
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
                    }
                    else {
                      wx.navigateTo({
                        url: './task?act=' + act + '&ops=' + ops + '&teamid=' + that._data.teamid + '&teamname=' + that._data.teamname + '&roleid=' + that._data.roleid
                      })
                    }
                  }

                },
                fail: function (res) {
                  //console.log(res);
                  wx.showToast({
                    title: '请在手机系统设置里，允许微信获得定位权限后再尝试，如果仍然失败，请将微信设为信任应用。',
                  })
                  app.globalData.getLocation = false
                  wx.navigateTo({
                    url: '../authlocation',
                  })
                },
                complete: function (res) {
                  //console.log(res);
                }
              });

            } else {
              if (that._data.roleid == 1) {
                // console.log(that._data.task.taskid)
                // console.log(that._data.act.aid)
                // console.log(that._data.teamid)
                wx.request({
                  url: app.globalData.config.apiUrl + 'index.php?act=checkPoiRedbagTask',
                  data: {
                    aid: that._data.act.aid,
                    openid: wx.getStorageSync('openid'),
                    taskid: that._data.task.taskid
                  },
                  method: 'POST',
                  success: (res) => {
                    let data = res.data
                    console.log(data)
                    if (data && data.status == 0) {
                      wx.navigateTo({
                        url: './redbagtask?aid=' + that._data.act.aid + '&taskid=' + that._data.task.taskid
                      })
                    } else {

                      wx.navigateTo({
                        url: './task?act=' + act + '&ops=' + ops + '&teamid=' + that._data.teamid + '&teamname=' + that._data.teamname + '&roleid=' + that._data.roleid
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
              }
              else {
                wx.navigateTo({
                  url: './task?act=' + act + '&ops=' + ops + '&teamid=' + that._data.teamid + '&teamname=' + that._data.teamname + '&roleid=' + that._data.roleid
                })
              }
            }
          }

        },
        fail: function (res) {
          wx.showToast({
            title: '未能获取当前位置，请检查GPS是否开启后再试',
            icon:'none'
          })
        }
      })

  },
  convertLatLng(taskid, latlng) {
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=convertLatLng',
      data: {
        taskid: taskid,
        latlng: latlng

      },
      method: 'POST',
      success: function (res) {
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

  //计算两点位置距离
  getDistance: function (lat1, lng1, lat2, lng2) {
    lat1 = lat1 || 0;
    lng1 = lng1 || 0;
    lat2 = lat2 || 0;
    lng2 = lng2 || 0;

    var rad1 = lat1 * Math.PI / 180.0;
    var rad2 = lat2 * Math.PI / 180.0;
    var a = rad1 - rad2;
    var b = lng1 * Math.PI / 180.0 - lng2 * Math.PI / 180.0;

    var r = 6378137;
    var distance = r * 2 * Math.asin(Math.sqrt(Math.pow(Math.sin(a / 2), 2) + Math.cos(rad1) * Math.cos(rad2) * Math.pow(Math.sin(b / 2), 2)));

    return distance;
  },
  getRad(d) {
    return d * PI / 180.0;
  },
  getFlatternDistance(lat1, lng1, lat2, lng2) {
    var f = this.getRad((lat1 + lat2) / 2);
    var g = this.getRad((lat1 - lat2) / 2);
    var l = this.getRad((lng1 - lng2) / 2);

    var sg = Math.sin(g);
    var sl = Math.sin(l);
    var sf = Math.sin(f);

    var s, c, w, r, d, h1, h2;
    var a = EARTH_RADIUS;
    var fl = 1 / 298.257;

    sg = sg * sg;
    sl = sl * sl;
    sf = sf * sf;

    s = sg * (1 - sl) + (1 - sf) * sl;
    c = (1 - sg) * (1 - sl) + sf * sl;

    w = Math.atan(Math.sqrt(s / c));
    r = Math.sqrt(s * c) / w;
    d = 2 * w * a;
    h1 = (3 * r - 1) / 2 / c;
    h2 = (3 * r + 1) / 2 / s;

    return d * (1 + fl * (h1 * sf * (1 - sg) - h2 * (1 - sf) * sg));
  },
  distanceByLnglat(lng1, lat1, lng2, lat2) {
    var radLat1 = this.Rad(lat1);
    var radLat2 = this.Rad(lat2);
    var a = radLat1 - radLat2;
    var b = this.Rad(lng1) - this.Rad(lng2);
    var s = 2 * Math.asin(Math.sqrt(Math.pow(Math.sin(a / 2), 2) + Math.cos(radLat1) * Math.cos(radLat2) * Math.pow(Math.sin(b / 2), 2)));
    s = s * 6378137.0; // 取WGS84标准参考椭球中的地球长半径(单位:m)
    s = Math.round(s * 10000) / 10000;
    console.log(s);
    return s
    // //下面为两点间空间距离（非球面体）
    // var value= Math.pow(Math.pow(lng1-lng2,2)+Math.pow(lat1-lat2,2),1/2);
    // alert(value);
  },

  Rad(d) {
    return d * Math.PI / 180.0;
  },
  onLoad: function (options) {
    console.log(options)
    let task = JSON.parse(options.ops)
    console.log(task)
    let act = JSON.parse(options.act)
    //console.log(act)
    this._data.act = act

    console.log(this._data.act.mapkey)
    let teamid = options.teamid
    this._data.teamid = teamid
    let roleid = options.roleid
    console.log(roleid)
    this._data.roleid = roleid
    console.log(this._data.roleid)
    this._data.teamname = options.teamname
    //console.log(roleid)
    wx.setNavigationBarTitle({
      title: task.displayorder + '号点-' + task.name
    })
    this._data.task = task

    if (this._data.task.latlng) {
      //console.log(task.latlng)
      let latlng = task.latlng.split(',')
      let lat = latlng[0]
      let lng = latlng[1]

      let marker = {
        'id': task.taskid,
        'alpha': 0.8,
        'latitude': lat,
        'longitude': lng,
        'callout': {
          'padding': 5,
          'borderRadius': 10,
          'content': task.pmemo.replace(/\\n/g, '\n'),
          'display': 'ALWAYS',
          'textAlign': 'left'
        }
      }
      let temp = []
      temp.push(marker)
      this.setData({
        lat: lat,
        lng: lng,
        marker: temp
      })
    } else {

      let mapApi = 'https://apis.map.qq.com/ws/coord/v1/translate?locations=' + task.poi + '&type=1&key=' + act.mapkey;
      wx.request({
        url: encodeURI(mapApi),
        header: {
          'cache-control': 'no-cache'
        },
        success: (res) => {
          let poi = res.data.locations[0]
          console.log(poi)
          let lat = poi.lat
          let lng = poi.lng

          let marker = {
            'id': task.taskid,
            'alpha': 0.8,
            'latitude': lat,
            'longitude': lng,
            'callout': {
              'padding': 5,
              'borderRadius': 10,
              'content': task.pmemo.replace(/\\n/g, '\n'),
              'display': 'ALWAYS',
              'textAlign': 'left'
            }
          }
          let temp = []
          temp.push(marker)
          this.setData({
            lat: lat,
            lng: lng,
            marker: temp
          })
          let latlng = '' + lat + ',' + lng + ''

          this.convertLatLng(task.taskid, latlng)
        },
        fail: (err) => {

        }
      })
    }




    this.mapCtx = wx.createMapContext('myMap')
    qqmapsdk = new QQMapWX({
      key: act.diskey
      //key: app.globalData.config.mapSubkey
    });
  },


})