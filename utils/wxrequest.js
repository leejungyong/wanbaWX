import regeneratorRuntime from 'runtime.js'
const wxRequest = async (url, params = {}) => {
    Object.assign(params, {
      token: wx.getStorageSync('token'),
      openid:wx.getStorageSync('openid'),
      method:'POST'
    })
    // 所有的请求，header默认携带token
    let header = params.header || {
      'Content-Type': 'application/json',
      'token': params.token || ''
    }
    let data = params.data || {}
    let method = params.method || 'GET'
    // hideLoading可以控制是否显示加载状态
    if (!params.hideLoading) {
     wx.showLoading({
       title: '加载中...',
     })
    }
    let res = await new Promise((resolve, reject) => {
      wx.request({
        url: url,
        method: method,
        data: data,
        header: header,
        success: (res) => {
          if (res && res.statusCode == 200) {
            resolve(res.data)
          } else {
            reject(res)
          }
        },
        fail: (err) => {
          reject(err)
        },
        complete: (e) => {
          wx.hideLoading()
        }
      })
    })
    return res
  }
  
  export {
    wxRequest
  }