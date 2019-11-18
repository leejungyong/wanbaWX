import regeneratorRuntime from 'runtime.js'
const wxUpload = async (url, filePath, name, params = {}) => {
  Object.assign(params, {
    token: wx.getStorageSync('token'),
    openid: wx.getStorageSync('openid')
  })
  let data = params.data || {}
 
  let res = await new Promise((resolve, reject) => {
    wx.request({
      url: url,
      filePath: filePath,
      name: name || 'file',
      formData: data,
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
        
      }
    })
  })
  return res
}

export {
  wxUpload
}