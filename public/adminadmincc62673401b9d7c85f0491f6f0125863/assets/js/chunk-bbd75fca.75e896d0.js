(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-bbd75fca"],{"0233":function(e,t,l){"use strict";l.r(t);var o=function(){var e=this,t=e.$createElement,l=e._self._c||t;return l("div",{staticClass:"ele-body"},[l("el-card",{attrs:{shadow:"never"}},[l("el-form",{staticClass:"ele-form-search",attrs:{model:e.table.where,"label-width":"77px"},nativeOn:{keyup:function(t){return!t.type.indexOf("key")&&e._k(t.keyCode,"enter",13,t.key,"Enter")?null:e.$refs.table.reload()},submit:function(e){e.preventDefault()}}},[l("el-row",{attrs:{gutter:15}},[l("el-col",{attrs:{md:6,sm:12}},[l("el-form-item",{attrs:{label:"版本号:"}},[l("el-input",{attrs:{placeholder:"请输入查询版本号",clearable:""},model:{value:e.table.where.version,callback:function(t){e.$set(e.table.where,"version",t)},expression:"table.where.version"}})],1)],1),l("el-col",{attrs:{md:6,sm:12}},[l("el-form-item",{attrs:{label:"设备:"}},[l("el-select",{attrs:{clearable:"",placeholder:"请选择查询设备"},model:{value:e.table.where.custom,callback:function(t){e.$set(e.table.where,"custom",t)},expression:"table.where.custom"}},e._l(e.options,(function(e){return l("el-option",{key:e.value,attrs:{label:e.label,value:e.value}})})),1)],1)],1),l("el-col",{attrs:{md:9,sm:12}},[l("div",{staticClass:"ele-form-actions"},[l("el-button",{staticClass:"ele-btn-icon",attrs:{type:"primary",icon:"el-icon-search"},on:{click:function(t){return e.$refs.table.reload()}}},[e._v("查询 ")]),l("el-button",{on:{click:function(t){(e.table.where={})&&e.$refs.table.reload()}}},[e._v("重置")])],1)])],1)],1),l("div",{staticClass:"ele-table-tool ele-table-tool-default"},[e.permission.includes("sys:appup:add")?l("el-button",{staticClass:"ele-btn-icon",attrs:{type:"primary",icon:"el-icon-plus",size:"small"},on:{click:function(t){e.showEdit=!0}}},[e._v("添加 ")]):e._e(),e.permission.includes("sys:appup:dall")?l("el-button",{staticClass:"ele-btn-icon",attrs:{type:"danger",icon:"el-icon-delete",size:"small"},on:{click:function(t){return e.remove()}}},[e._v("批量删除 ")]):e._e()],1),l("ele-data-table",{ref:"table",attrs:{config:e.table,choose:e.choose,height:"calc(100vh - 315px)","highlight-current-row":""},on:{"update:choose":function(t){e.choose=t}},scopedSlots:e._u([{key:"default",fn:function(t){var o=t.index;return[l("el-table-column",{attrs:{type:"selection",width:"45",align:"center",fixed:"left"}}),l("el-table-column",{attrs:{type:"index",index:o,label:"编号",width:"60",align:"center",fixed:"left","show-overflow-tooltip":""}}),l("el-table-column",{attrs:{prop:"url",label:"升级apk链接地址",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),l("el-table-column",{attrs:{prop:"version",label:"版本号",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),l("el-table-column",{attrs:{prop:"text",label:"更新信息",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),l("el-table-column",{attrs:{label:"设备",sortable:"custom","show-overflow-tooltip":"","min-width":"120"},scopedSlots:e._u([{key:"default",fn:function(t){var o=t.row;return[l("div",[e._v(e._s(1==o.shebei?"Android":"IOS"))])]}}],null,!0)}),l("el-table-column",{attrs:{label:"创建时间",sortable:"custom","show-overflow-tooltip":"","min-width":"160"},scopedSlots:e._u([{key:"default",fn:function(t){var l=t.row;return[e._v(e._s(e._f("toDateString")(1e3*l.create_time)))]}}],null,!0)}),l("el-table-column",{attrs:{label:"更新时间",sortable:"custom","show-overflow-tooltip":"","min-width":"160"},scopedSlots:e._u([{key:"default",fn:function(t){var l=t.row;return[e._v(e._s(e._f("toDateString")(1e3*l.update_time)))]}}],null,!0)}),l("el-table-column",{attrs:{label:"操作",width:"130px",align:"center",resizable:!1,fixed:"right"},scopedSlots:e._u([{key:"default",fn:function(t){var o=t.row;return[e.permission.includes("sys:appup:edit")?l("el-link",{attrs:{icon:"el-icon-edit",type:"primary",underline:!1},on:{click:function(t){return e.edit(o)}}},[e._v("修改")]):e._e(),l("el-popconfirm",{staticClass:"ele-action",attrs:{title:"确定要删除此app升级吗？"},on:{confirm:function(t){return e.remove(o)}}},[e.permission.includes("sys:appup:delete")?l("el-link",{attrs:{slot:"reference",icon:"el-icon-delete",type:"danger",underline:!1},slot:"reference"},[e._v("删除")]):e._e()],1)]}}],null,!0)})]}}])})],1),l("el-dialog",{attrs:{title:(e.editForm.id,"修改app升级"),visible:e.showEdit,width:"550px","destroy-on-close":!0,"lock-scroll":!1},on:{"update:visible":function(t){e.showEdit=t},closed:function(t){e.editForm={}}}},[l("el-form",{ref:"editForm",attrs:{model:e.editForm,rules:e.editRules,"label-width":"auto"}},[l("el-form-item",{attrs:{label:"升级apk链接地址：",prop:"url"}},[l("el-input",{attrs:{placeholder:"请输入升级apk链接地址",clearable:""},model:{value:e.editForm.url,callback:function(t){e.$set(e.editForm,"url",t)},expression:"editForm.url"}})],1),l("el-form-item",{attrs:{label:"版本号：",prop:"version"}},[l("el-input",{attrs:{placeholder:"请输入版本号",clearable:""},model:{value:e.editForm.version,callback:function(t){e.$set(e.editForm,"version",t)},expression:"editForm.version"}})],1),l("el-form-item",{attrs:{label:"更新信息：",prop:"text"}},[l("el-input",{attrs:{placeholder:"请输入更新信息",clearable:""},model:{value:e.editForm.text,callback:function(t){e.$set(e.editForm,"text",t)},expression:"editForm.text"}})],1),l("el-form-item",{attrs:{label:"设备",prop:"shebei"}},[l("el-select",{attrs:{placeholder:"请选择"},model:{value:e.editForm.shebei,callback:function(t){e.$set(e.editForm,"shebei",t)},expression:"editForm.shebei"}},e._l(e.options,(function(e){return l("el-option",{key:e.value,attrs:{label:e.label,value:e.value}})})),1)],1)],1),l("div",{attrs:{slot:"footer"},slot:"footer"},[l("el-button",{on:{click:function(t){e.showEdit=!1}}},[e._v("取消")]),l("el-button",{attrs:{type:"primary"},on:{click:e.save}},[e._v("保存")])],1)],1)],1)},r=[],s=(l("d81d"),l("5530")),a=l("2f62"),i={name:"SysAppup",data:function(){return{table:{url:"/appup/index",where:{}},choose:[],showEdit:!1,editForm:{},editRules:{url:[{required:!0,message:"请输入升级apk链接地址",trigger:"blur"}],version:[{required:!0,message:"请输入版本号",trigger:"blur"}],text:[{required:!0,message:"请输入更新信息",trigger:"blur"}],shebei:[{required:!0,message:"请输入设备 1=Android 2=IOS",trigger:"blur"}]},options:[{value:1,label:"Android"},{value:2,label:"IOS"}]}},computed:Object(s["a"])({},Object(a["b"])(["permission"])),mounted:function(){},methods:{edit:function(e){var t=this;this.$http.get("/appup/info?id="+e.id).then((function(e){0===e.data.code?(t.editForm=e.data.data,t.showEdit=!0):t.$message.error(e.data.msg)})).catch((function(e){t.$message.error(e.message)}))},save:function(){var e=this;this.$refs["editForm"].validate((function(t){if(!t)return!1;var l=e.$loading({lock:!0});e.$http.post("/appup/edit",e.editForm).then((function(t){l.close(),0===t.data.code?(e.showEdit=!1,e.$message({type:"success",message:t.data.msg}),e.$refs.table.reload()):e.$message.error(t.data.msg)})).catch((function(t){l.close(),e.$message.error(t.message)}))}))},remove:function(e){var t=this;if(console.log(e),e){var l=this.$loading({lock:!0});this.$http.post("/appup/delete",{id:e.id}).then((function(e){l.close(),0===e.data.code?(t.$message({type:"success",message:e.data.msg}),t.$refs.table.reload()):t.$message.error(e.data.msg)})).catch((function(e){l.close(),t.$message.error(e.message)}))}else{if(0===this.choose.length)return this.$message.error("请至少选择一条数据");var o=this.choose.map((function(e){return e.id}));this.$confirm("确定要删除选中的app升级吗?","提示",{type:"warning"}).then((function(){var e=t.$loading({lock:!0});t.$http.post("/appup/delete",{id:o}).then((function(l){e.close(),0===l.data.code?(t.$message({type:"success",message:l.data.msg}),t.$refs.table.reload()):t.$message.error(l.data.msg)})).catch((function(l){e.close(),t.$message.error(l.message)}))})).catch((function(){return 0}))}}}},n=i,c=(l("f265"),l("2877")),u=Object(c["a"])(n,o,r,!1,null,"d811a014",null);t["default"]=u.exports},4454:function(e,t,l){},f265:function(e,t,l){"use strict";l("4454")}}]);