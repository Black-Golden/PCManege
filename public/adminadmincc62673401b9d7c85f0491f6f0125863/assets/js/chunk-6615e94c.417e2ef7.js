(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-6615e94c"],{"1b96":function(e,t,o){},"92cd":function(e,t,o){"use strict";o.r(t);var l=function(){var e=this,t=e.$createElement,o=e._self._c||t;return o("div",{staticClass:"ele-body"},[o("el-card",{attrs:{shadow:"never"}},[o("el-form",{staticClass:"ele-form-search",attrs:{model:e.table.where,"label-width":"77px"},nativeOn:{keyup:function(t){return!t.type.indexOf("key")&&e._k(t.keyCode,"enter",13,t.key,"Enter")?null:e.$refs.table.reload()},submit:function(e){e.preventDefault()}}},[o("el-row",{attrs:{gutter:15}},[o("el-col",{attrs:{md:6,sm:12}},[o("div",{staticClass:"ele-form-actions"},[o("el-button",{staticClass:"ele-btn-icon",attrs:{type:"primary",icon:"el-icon-search"},on:{click:function(t){return e.$refs.table.reload()}}},[e._v("查询 ")]),o("el-button",{on:{click:function(t){(e.table.where={})&&e.$refs.table.reload()}}},[e._v("重置")])],1)])],1)],1),o("div",{staticClass:"ele-table-tool ele-table-tool-default"},[e.permission.includes("sys:quantpoint:add")?o("el-button",{staticClass:"ele-btn-icon",attrs:{type:"primary",icon:"el-icon-plus",size:"small"},on:{click:function(t){e.showEdit=!0}}},[e._v("添加 ")]):e._e(),e.permission.includes("sys:quantpoint:dall")?o("el-button",{staticClass:"ele-btn-icon",attrs:{type:"danger",icon:"el-icon-delete",size:"small"},on:{click:function(t){return e.remove()}}},[e._v("批量删除 ")]):e._e()],1),o("ele-data-table",{ref:"table",attrs:{config:e.table,choose:e.choose,height:"calc(100vh - 315px)","highlight-current-row":""},on:{"update:choose":function(t){e.choose=t}},scopedSlots:e._u([{key:"default",fn:function(t){var l=t.index;return[o("el-table-column",{attrs:{type:"selection",width:"45",align:"center",fixed:"left"}}),o("el-table-column",{attrs:{type:"index",index:l,label:"编号",width:"60",align:"center",fixed:"left","show-overflow-tooltip":""}}),o("el-table-column",{attrs:{prop:"user_id",label:"",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),o("el-table-column",{attrs:{prop:"num",label:"数量",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),o("el-table-column",{attrs:{prop:"memo",label:"备注",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),o("el-table-column",{attrs:{prop:"last_num",label:"当前余额",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),o("el-table-column",{attrs:{prop:"type_id",label:"类型1充值2手续费支出3开通AI",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),o("el-table-column",{attrs:{prop:"payment_id",label:"收支类型1支出2收入",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),o("el-table-column",{attrs:{label:"创建时间",sortable:"custom","show-overflow-tooltip":"","min-width":"160"},scopedSlots:e._u([{key:"default",fn:function(t){var o=t.row;return[e._v(e._s(e._f("toDateString")(1e3*o.create_time)))]}}],null,!0)}),o("el-table-column",{attrs:{label:"更新时间",sortable:"custom","show-overflow-tooltip":"","min-width":"160"},scopedSlots:e._u([{key:"default",fn:function(t){var o=t.row;return[e._v(e._s(e._f("toDateString")(1e3*o.update_time)))]}}],null,!0)}),o("el-table-column",{attrs:{label:"操作",width:"130px",align:"center",resizable:!1,fixed:"right"},scopedSlots:e._u([{key:"default",fn:function(t){var l=t.row;return[e.permission.includes("sys:quantpoint:edit")?o("el-link",{attrs:{icon:"el-icon-edit",type:"primary",underline:!1},on:{click:function(t){return e.edit(l)}}},[e._v("修改")]):e._e(),o("el-popconfirm",{staticClass:"ele-action",attrs:{title:"确定要删除此点卡流水吗？"},on:{confirm:function(t){return e.remove(l)}}},[e.permission.includes("sys:quantpoint:delete")?o("el-link",{attrs:{slot:"reference",icon:"el-icon-delete",type:"danger",underline:!1},slot:"reference"},[e._v("删除")]):e._e()],1)]}}],null,!0)})]}}])})],1),o("el-dialog",{attrs:{title:(e.editForm.id,"修改点卡流水"),visible:e.showEdit,width:"450px","destroy-on-close":!0,"lock-scroll":!1},on:{"update:visible":function(t){e.showEdit=t},closed:function(t){e.editForm={}}}},[o("el-form",{ref:"editForm",attrs:{model:e.editForm,rules:e.editRules,"label-width":"100px"}},[o("el-form-item",{attrs:{label:"：",prop:"user_id"}},[o("el-input-number",{staticClass:"ele-fluid ele-text-left",attrs:{"controls-position":"right",min:0,placeholder:"请输入"},model:{value:e.editForm.user_id,callback:function(t){e.$set(e.editForm,"user_id",t)},expression:"editForm.user_id"}})],1),o("el-form-item",{attrs:{label:"数量：",prop:"num"}},[o("el-input-number",{staticClass:"ele-fluid ele-text-left",attrs:{"controls-position":"right",min:0,placeholder:"请输入数量"},model:{value:e.editForm.num,callback:function(t){e.$set(e.editForm,"num",t)},expression:"editForm.num"}})],1),o("el-form-item",{attrs:{label:"备注：",prop:"memo"}},[o("el-input",{attrs:{placeholder:"请输入备注",clearable:""},model:{value:e.editForm.memo,callback:function(t){e.$set(e.editForm,"memo",t)},expression:"editForm.memo"}})],1),o("el-form-item",{attrs:{label:"当前余额：",prop:"last_num"}},[o("el-input-number",{staticClass:"ele-fluid ele-text-left",attrs:{"controls-position":"right",min:0,placeholder:"请输入当前余额"},model:{value:e.editForm.last_num,callback:function(t){e.$set(e.editForm,"last_num",t)},expression:"editForm.last_num"}})],1),o("el-form-item",{attrs:{label:"类型1充值2手续费支出3开通AI：",prop:"type_id"}},[o("el-input-number",{staticClass:"ele-fluid ele-text-left",attrs:{"controls-position":"right",min:0,placeholder:"请输入类型1充值2手续费支出3开通AI"},model:{value:e.editForm.type_id,callback:function(t){e.$set(e.editForm,"type_id",t)},expression:"editForm.type_id"}})],1),o("el-form-item",{attrs:{label:"收支类型1支出2收入：",prop:"payment_id"}},[o("el-input-number",{staticClass:"ele-fluid ele-text-left",attrs:{"controls-position":"right",min:0,placeholder:"请输入收支类型1支出2收入"},model:{value:e.editForm.payment_id,callback:function(t){e.$set(e.editForm,"payment_id",t)},expression:"editForm.payment_id"}})],1)],1),o("div",{attrs:{slot:"footer"},slot:"footer"},[o("el-button",{on:{click:function(t){e.showEdit=!1}}},[e._v("取消")]),o("el-button",{attrs:{type:"primary"},on:{click:e.save}},[e._v("保存")])],1)],1)],1)},i=[],s=(o("d81d"),o("5530")),r=o("2f62"),n={name:"SysQuantPoint",data:function(){return{table:{url:"/quantpoint/index",where:{}},choose:[],showEdit:!1,editForm:{},editRules:{user_id:[{required:!0,message:"请输入",trigger:"blur"}],num:[{required:!0,message:"请输入数量",trigger:"blur"}],memo:[{required:!0,message:"请输入备注",trigger:"blur"}],last_num:[{required:!0,message:"请输入当前余额",trigger:"blur"}],type_id:[{required:!0,message:"请输入类型1充值2手续费支出3开通AI",trigger:"blur"}],payment_id:[{required:!0,message:"请输入收支类型1支出2收入",trigger:"blur"}]}}},computed:Object(s["a"])({},Object(r["b"])(["permission"])),mounted:function(){},methods:{edit:function(e){var t=this;this.$http.get("/quantpoint/info?id="+e.id).then((function(e){0===e.data.code?(t.editForm=e.data.data,t.showEdit=!0):t.$message.error(e.data.msg)})).catch((function(e){t.$message.error(e.message)}))},save:function(){var e=this;this.$refs["editForm"].validate((function(t){if(!t)return!1;var o=e.$loading({lock:!0});e.$http.post("/quantpoint/edit",e.editForm).then((function(t){o.close(),0===t.data.code?(e.showEdit=!1,e.$message({type:"success",message:t.data.msg}),e.$refs.table.reload()):e.$message.error(t.data.msg)})).catch((function(t){o.close(),e.$message.error(t.message)}))}))},remove:function(e){var t=this;if(console.log(e),e){var o=this.$loading({lock:!0});this.$http.post("/quantpoint/delete",{id:e.id}).then((function(e){o.close(),0===e.data.code?(t.$message({type:"success",message:e.data.msg}),t.$refs.table.reload()):t.$message.error(e.data.msg)})).catch((function(e){o.close(),t.$message.error(e.message)}))}else{if(0===this.choose.length)return this.$message.error("请至少选择一条数据");var l=this.choose.map((function(e){return e.id}));this.$confirm("确定要删除选中的点卡流水吗?","提示",{type:"warning"}).then((function(){var e=t.$loading({lock:!0});t.$http.post("/quantpoint/delete",{id:l}).then((function(o){e.close(),0===o.data.code?(t.$message({type:"success",message:o.data.msg}),t.$refs.table.reload()):t.$message.error(o.data.msg)})).catch((function(o){e.close(),t.$message.error(o.message)}))})).catch((function(){return 0}))}}}},a=n,c=(o("c4f8"),o("2877")),d=Object(c["a"])(a,l,i,!1,null,"5223e8b3",null);t["default"]=d.exports},c4f8:function(e,t,o){"use strict";o("1b96")}}]);