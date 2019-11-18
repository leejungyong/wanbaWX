const app = getApp()
let arr=[],arr2=[]

var aid=null
Page({
  data: {
    uploadUrl: app.globalData.config.uploadUrl,
    swiper: null,
    list: null,
    circular: true,
    indicatorDots: false,
    indicatorcolor: "#000",
    vertical: false,
    autoplay: true,
    interval: 2500,
    duration: 100,
    imgheights: [],
    imgwidth: 750,
    current: 0,
    imgwidth: 0,
    imgheight: 160,
    navbar: ['热门线路','最新线路' ],
    currentTab: 0,
    openSide:false,
    display:'none',
    translate:'',
    windowWidth: wx.getSystemInfoSync().windowWidth,
    cityList:[],
    typeList: [],
    choosecity:[],
    choosetype:[],
    keyword:'',
    inputValue:'',
    page:0,
    pagesize:'5',
    pages:null,
    order:'0',
    init:true
  },
  stopLoadMoreTiem:false,// 阻止多次触发 需要的变量
  //选择城市
  chooseCity(e){
    let that=this
    let index = e.target.dataset.idx
    let city = that.data.cityList[index].name
    let cityarr=that.data.cityList
    cityarr[index].checked=!cityarr[index].checked
    if(cityarr[index].checked){
      if (arr.indexOf(city) == -1) {
        arr.push(city)
      }
    }else{
      arr.splice(arr.indexOf(city),1)
    }
    this.setData({
      choosecity:arr,
      cityList:cityarr
    })
    console.log(that.data.choosecity)

  },
  //选择类别
  chooseType(e){
    let that = this
    let index = e.target.dataset.idx
    let type = that.data.typeList[index].name
    let typearr = that.data.typeList
    typearr[index].checked = !typearr[index].checked
    if (typearr[index].checked) {
      if (arr2.indexOf(type) == -1) {
        arr2.push(type)
      }
    } else {
      arr2.splice(arr2.indexOf(type), 1)
    }
    this.setData({
      choosetype: arr2,
      typeList: typearr
    })
    console.log(that.data.choosetype)
  },
  //关键词
  changeKeyWord(e){
    console.log(e.detail.value)
    this.setData({
      keyword:e.detail.value
    })
  },

  //重置按钮
  resetButton(){
    let cityarr=this.data.cityList
    let typearr=this.data.typeList
    cityarr.map(item=>{
      item.checked=false
    })
    typearr.map(item=>{
      item.checked=false
    })
    arr=[]
    arr2=[]
    this.setData({
      choosecity:[],
      choosetype:[],
      keyword:'',
      page:0,
      init:true,
      cityList:cityarr,
      typeList:typearr
    })
    this.fetch()
  },
  //确定按钮
  sureButton(){
    let that = this
    
    that.setData({
      page: 0,
      init:true,
      display: 'none'
    })
    that.fetch()
    // arr=[]
    // arr2=[]
  },

  //显示侧边栏
  showSide(){
    let that=this
    this.setData({
      openSide:true,
      display:'block',
    })
  },

  //关闭侧边栏
  hideSide(){
    this.setData({
      openSide: false,
      display: 'none',
      translate: ''
    })
  },
  //预览模板
  preview() {
    wx.navigateTo({
      url: './preview',
    })
  },
  
  to: function (e) {
    let that = this
    let id = e.currentTarget.id
    let url = that.data.swiper[id].url
    if (url.indexOf('/pages/') > -1) {
      wx.navigateTo({
        url: url
      })
    } else {
      wx.navigateTo({
        url: '/pages/index/showWx?url=' + url
      })
    }
  },
  imageLoad: function (e) { //获取图片真实宽度  
    var imgwidth = e.detail.width,
      imgheight = e.detail.height,
      //宽高比  
      ratio = imgwidth / imgheight;
    //console.log(imgwidth, imgheight)
    //计算的高度值  
    var viewHeight = 750 / ratio;
    var imgheight = viewHeight;
    var imgheights = this.data.imgheights;
    //把每一张图片的对应的高度记录到数组里  
    imgheights[e.target.dataset.id] = imgheight;
    this.setData({
      imgheights: imgheights
    })
  },
  picLoad: function (e) {
    var _this = this;
    var $width = e.detail.width, //获取图片真实宽度
      $height = e.detail.height,
      ratio = $width / $height; //图片的真实宽高比例
    var viewWidth = 640, //设置图片显示宽度，
      viewHeight = 640 / ratio; //计算的高度值   
    this.setData({
      imgwidth: viewWidth,
      imgheight: viewHeight
    })

  },


  bindchange: function (e) {
    // console.log(e.detail.current)
    this.setData({
      current: e.detail.current
    })
  },
  to: function (e) {
    let that = this
    let id = e.currentTarget.id
    let tid = that.data.list[id].aid
    
    wx.navigateTo({
      url: './showContent?aid=' + aid+'&tid='+tid
    })
  },
  navbarTap: function (e) {

    let arr = this.data.cityList.map((item, index) => {
      item.checked = false
      return item
    })
    this.setData({
      currentTab: e.currentTarget.dataset.idx,
      order: e.currentTarget.dataset.idx,
      keyword: '',
      choosetype: [],
      choosecity: [],
      page: 0,
      list: [],
      cityList: arr
    })
    this.fetch()
    // console.log(this.data.order)
  },
  fetch() {
    let that = this
    this.stopLoadMoreTiem = true
    wx.showLoading({
      title: '加载中',
    })
    let data = {
      city: that.data.choosecity,
      tag: that.data.choosetype,
      key: that.data.keyword,
      order: that.data.order,
      currentpage: that.data.page,
      pagesize: that.data.pagesize,
      aid:aid
    }
    console.log(data)
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=importTemplate',
      data: data,
      method: 'POST',
      success: (res) => {
        that.stopLoadMoreTiem = false
        console.log(res.data)
        let data = res.data
       
        let cities = that.data.cityList.length > 0 ? that.data.cityList :data.cities.map(item => {
          return { name: item, checked: false }
        })
        let tags = that.data.typeList.length > 0 ? that.data.typeList :data.tags.map(item => {
          return { name: item, checked: false }
        })
        let pagenum = Math.ceil(data.total / that.data.pagesize)
        if (that.data.init){
          that.setData({
            swiper: data.swiper,
            list: data.list,
            typeList:tags,
            cityList:cities,
            pages:pagenum,
            init:false
          })
        }else{
          var newData = that.data.list.concat(data.list)
          that.setData({
            list:newData,
            swiper:data.swiper
          })
        }
        wx.hideLoading()
      },
      fail: (err) => {
        that.stopLoadMoreTiem = false
        wx.hideLoading()
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },
  onLoad: function (options) {
    aid=options.aid
    console.log(aid)
    arr = []
    arr2 = []
    this.fetch()
  },
  /**
 * 页面上拉触底事件的处理函数
 */
  onReachBottom: function () {
    let that=this
 
    if(!that.stopLoadMoreTiem&&(that.data.page<that.data.pages-1)){
      this.setData({
        page: that.data.page + 1
      })
      this.fetch()
    }

  },

})