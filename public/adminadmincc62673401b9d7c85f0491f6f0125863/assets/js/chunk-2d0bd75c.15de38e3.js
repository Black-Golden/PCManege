(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-2d0bd75c"],{"2ba7":function(e,t,l){"use strict";l.r(t);var a=function(){var e=this,t=e.$createElement,l=e._self._c||t;return l("div",{staticClass:"ele-body"},[l("el-card",{attrs:{shadow:"never"}},[l("el-form",{staticClass:"ele-form-search",attrs:{model:e.table.where,"label-width":"77px"},nativeOn:{keyup:function(t){return!t.type.indexOf("key")&&e._k(t.keyCode,"enter",13,t.key,"Enter")?null:e.$refs.table.reload()},submit:function(e){e.preventDefault()}}},[l("el-row",{attrs:{gutter:15}},[l("el-col",{attrs:{md:6,sm:12}},[l("el-form-item",{attrs:{label:"用户账号:"}},[l("el-input",{attrs:{placeholder:"请输入用户账号",clearable:""},model:{value:e.table.where.username,callback:function(t){e.$set(e.table.where,"username",t)},expression:"table.where.username"}})],1)],1),l("el-col",{attrs:{md:6,sm:12}},[l("div",{staticClass:"ele-form-actions"},[l("el-button",{staticClass:"ele-btn-icon",attrs:{type:"primary",icon:"el-icon-search"},on:{click:function(t){return e.$refs.table.reload()}}},[e._v("查询 ")]),e.permission.includes("sys:loginlog:export")?l("el-button",{staticClass:"ele-btn-icon",attrs:{icon:"el-icon-download",type:"success"},on:{click:e.exportData}},[e._v("导出日志")]):e._e()],1)])],1)],1),l("ele-data-table",{ref:"table",attrs:{config:e.table,height:"calc(100vh - 260px)","highlight-current-row":""},scopedSlots:e._u([{key:"default",fn:function(t){var a=t.index;return[l("el-table-column",{attrs:{type:"index",index:a,label:"编号",width:"60",align:"center",fixed:"left","show-overflow-tooltip":""}}),l("el-table-column",{attrs:{prop:"title",label:"日志标题",align:"center","show-overflow-tooltip":"","min-width":"100"}}),l("el-table-column",{attrs:{prop:"username",label:"登录账号",align:"center","show-overflow-tooltip":"","min-width":"100"}}),l("el-table-column",{attrs:{prop:"method",label:"请求方式",align:"center","show-overflow-tooltip":"","min-width":"100"}}),l("el-table-column",{attrs:{prop:"module",label:"操作模块",align:"center","show-overflow-tooltip":"","min-width":"100"}}),l("el-table-column",{attrs:{prop:"action",label:"操作方法",align:"center","show-overflow-tooltip":"","min-width":"150"}}),l("el-table-column",{attrs:{prop:"url",label:"操作URL",align:"center","show-overflow-tooltip":"","min-width":"200"}}),l("el-table-column",{attrs:{prop:"param",label:"请求参数",align:"center","show-overflow-tooltip":"","min-width":"200"}}),l("el-table-column",{attrs:{prop:"ip",label:"操作IP",align:"center","show-overflow-tooltip":"","min-width":"130"}}),l("el-table-column",{attrs:{prop:"type",label:"操作类型",align:"center","min-width":"110","show-overflow-tooltip":""},scopedSlots:e._u([{key:"default",fn:function(e){var t=e.row;return[l("ele-dot",{attrs:{type:["","success","warning"][t.type-1],ripple:0===t.type,text:["登录系统","注销系统","操作日志"][t.type-1]}})]}}],null,!0)}),l("el-table-column",{attrs:{prop:"create_time",label:"操作时间",align:"center","min-width":"160","show-overflow-tooltip":""},scopedSlots:e._u([{key:"default",fn:function(t){var l=t.row;return[e._v(e._s(e._f("toDateString")(1e3*l.create_time)))]}}],null,!0)}),l("el-table-column",{attrs:{label:"操作",width:"100px",align:"center",resizable:!1,fixed:"right"},scopedSlots:e._u([{key:"default",fn:function(t){var a=t.row;return[l("el-popconfirm",{staticClass:"ele-action",attrs:{title:"确定要删除此登录日志吗？"},on:{confirm:function(t){return e.remove(a)}}},[e.permission.includes("sys:loginlog:delete")?l("el-link",{attrs:{slot:"reference",icon:"el-icon-delete",type:"danger",underline:!1},slot:"reference"},[e._v("删除")]):e._e()],1)]}}],null,!0)})]}}])})],1)],1)},o=[],n=(l("4160"),l("159b"),l("5530")),r=l("1146"),i=l.n(r),s=l("2f62"),c={name:"SysLoginRecord",data:function(){return{table:{url:"/loginlog/index",where:{}},daterange:"",pickerOptions:{shortcuts:[{text:"最近一周",onClick:function(e){var t=new Date,l=new Date;l.setTime(l.getTime()-6048e5),e.$emit("pick",[l,t])}},{text:"最近一个月",onClick:function(e){var t=new Date,l=new Date;l.setTime(l.getTime()-2592e6),e.$emit("pick",[l,t])}},{text:"最近三个月",onClick:function(e){var t=new Date,l=new Date;l.setTime(l.getTime()-7776e6),e.$emit("pick",[l,t])}}]}}},computed:Object(n["a"])({},Object(s["b"])(["permission"])),methods:{onDateRangeChoose:function(){this.daterange?(this.table.where.createTimeStart=this.daterange[0],this.table.where.createTimeEnd=this.daterange[1]):(this.table.where.createTimeStart=null,this.table.where.createTimeEnd=null)},exportData:function(){var e=this,t=[["日志标题","登录账号","请求方式","操作模块","操作方法","操作URL","请求参数","操作IP","操作类型","登录时间"]],l=this.$loading({lock:!0});this.$http.get("/loginlog/index?page=1&limit=2000").then((function(a){if(l.close(),0===a.data.code){a.data.data.forEach((function(l){t.push([l.title,l.username,l.method,l.module,l.action,l.url,l.param,l.ip,["登录系统","注销系统"][l.type],e.$util.toDateString(l.create_time)])}));var o=i.a.utils.aoa_to_sheet(t);e.$util.exportSheet(i.a,o,"登录日志")}else e.$message.error(a.data.msg)})).catch((function(t){l.close(),e.$message.error(t.message)}))},remove:function(e){var t=this,l=this.$loading({lock:!0});this.$http.post("/loginlog/delete",{id:e.id}).then((function(e){l.close(),0===e.data.code?(t.$message({type:"success",message:e.data.msg}),t.$refs.table.reload()):t.$message.error(e.data.msg)})).catch((function(e){l.close(),t.$message.error(e.message)}))}}},u=c,p=l("2877"),d=Object(p["a"])(u,a,o,!1,null,"ffb2ac4e",null);t["default"]=d.exports}}]);