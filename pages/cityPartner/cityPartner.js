// pages/cityPartner/cityPartner.js
//编辑页面
import regeneratorRuntime from '../../utils/runtime.js'
import {wxRequest} from '../../utils/wxrequest.js'
const app=getApp()

Page({
  /**
   * 页面的初始数据
   */
  data: {
    companyName:'', //公司全称
    address:'',     //所在区域
    addressDetail:'', //详细地址
    orgCode:'',     //机构代码
    corporate:'',   //公司法人
    telephone:'',   //联系电话
    image:'',     //营业执照
    image_index:0,  //判断图片状态，是未上传还是已上传的
    show:true,
    region:['浙江省','杭州市','西湖区'] ,  //默认选中的省市区
    customItem:'',
    buttonText:'提交',    //提交按钮的文案，根据入口不同显示 申请 或 确认修改
    modalText:'成功提交合伙人申请',
    status:''
  },

  /**
 * 上传营业执照
 */
  uploadLicense: function () {
    let that = this
    wx.chooseImage({
      count:1,
      sizeType: ['original', 'compressed'], //可选择原图或压缩后的图片  
      sourceType: ['album', 'camera'], //可选择性开放访问相册、相机
      success: function (res) {
       let pic= res.tempFilePaths[0].toLowerCase()
        if (pic.indexOf('.jpg') == -1) {
          wx.showToast({
            title: '请上传jpg格式图片',
            icon: 'none',
            mask: true
          })

        }
        else{
        that.setData({
          image: pic,
          image_index:0
        })
        }
      },
    })
  },

  /**
   * 申请按钮
   */
  async apply() {
    let that=this
    if(that.data.companyName==''){ 
      wx.showToast({
        title: '请填写公司名称！',
        icon:'none'
      })
    }else if(that.data.orgCode==''){
      wx.showToast({
        title: '请填写机构代码！',
        icon: 'none'
      })
    }else if(that.data.corporate==''){
      wx.showToast({
        title: '请填写公司法人！',
        icon: 'none'
      })
    }else if(that.data.telephone==''){
      wx.showToast({
        title: '请填写联系电话！',
        icon: 'none'
      })
    }else if(that.data.image==''){
      wx.showToast({
        title: '请上传营业执照！',
        icon: 'none'
      })
    }else if(that.data.address==''){
      wx.showToast({
        title: '请选择所在区域！',
        icon: 'none'
      })
    } else if (that.data.addressDetail == '') {
      wx.showToast({
        title: '请填写详细地址！',
        icon: 'none'
      })
    } else{
      // wx.showLoading({
      //   title: '提交中',
      //   mask:true
      // })

     let paramsObj= {
       data:{
         companyName: that.data.companyName,
         orgCode: that.data.orgCode,
         corporate: that.data.corporate,
         telephone: that.data.telephone,
         openid: wx.getStorageSync('openid'),
         address: that.data.address,
         addressDetail: that.data.addressDetail
       },

      }
      await wxRequest(app.globalData.config.apiUrl+'index.php?act=applyAgent',paramsObj).then(res=>{
        let id=res.data.id
        let state=res.data.status
        console.log(res)
        if(res&&that.data.image_index==0){
          wx.uploadFile({
            url: app.globalData.config.apiUrl +'uploadAgentPic.php',
            filePath: that.data.image,
            name: 'file',
            formData:{
              openid: wx.getStorageSync('openid'),
              id:id
            },
            success:res=>{
              console.log('图片上传成功！')
              state=res.data.status
            },
            fail:res=>{
              console.log('图片上传失败！')
            }
          })
        }

        let pages = getCurrentPages()
        let pre = pages[pages.length - 2]
        pre.setData({
          type: '',
          status: state ? state:0
        })
        that.setData({
          show: false
        })
      })

      // wx.uploadFile({
        //   url: 'https://www.wondfun.com/wanba/api/applyAgent.php',
        //   filePath: that.data.image,
        //   name: 'file',
          // formData: {
          //   companyName: that.data.companyName,
          //   orgCode: that.data.orgCode,
          //   corporate: that.data.corporate,
          //   telephone: that.data.telephone,
          //   openid: that.data.openid,
          //   address:that.data.address,
          //   addressDetail:that.data.addressDetail
          // },
        //   success(res) {
          // let pages=getCurrentPages()
          // let pre=pages[pages.length-2]
          // pre.setData({
          //     type:''
          // })
          // pre.init()
          // wx.hideLoading()
          // that.setData({
          //     show:false
          //   })
        //   },
        //   fail(res){
        //     console.log('无图片')
        //   }
      // })
    }
 

  },
  updateCompanyName(e){
    this.setData({
      companyName:e.detail.value
    })
  },
  updateOrgCode(e){
    this.setData({
      orgCode:e.detail.value
    })
  },
  updateCorporate(e){
    this.setData({
      corporate:e.detail.value
    })
  },
  updateTelephone(e){
    this.setData({
      telephone:e.detail.value
    })
  },
  updateAddressDetail(e){
    this.setData({
      addressDetail:e.detail.value
    })
  },

  /**
   * 初始化页面时获取合伙人信息
  */
  getInfo(){
    let that=this
    wx.request({
      url: app.globalData.config.apiUrl +'index.php?act=queryAgentApply',
      method:'POST',
      data:{
        openid:wx.getStorageSync('openid')
      },
      success(res){
        if(res.data.mode){
          that.setData({
            companyName: res.data.company,
            orgCode: res.data.orgcode,
            corporate: res.data.corporate,
            telephone: res.data.tel,
            address: res.data.city,
            addressDetail: res.data.address,
            image: res.data.pic,
            image_index:1
          })
        }
      }

    })
  },
  /**
   * 点击返回按钮
   */
  clickReturn(){
    wx.navigateBack({})
    this.setData({
      show:true
    })

  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    if(options.alter!=undefined){
      if(options.alter==1){
        this.setData({
          buttonText: '确认修改',
          modalText: '资料修改成功'
        })
      }
    }
   
    this.getInfo()
  },

  /**
   * 省市区选择操作
   */
  bindRegionChange(e){
    let that=this
    let str=e.detail.value.join(' ')
    that.setData({
      region: e.detail.value,
      address:str
    })
  },



})