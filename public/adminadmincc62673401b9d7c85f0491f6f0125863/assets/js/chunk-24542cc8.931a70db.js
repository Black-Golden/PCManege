(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-24542cc8"],{"0c3c":function(e,t,o){"use strict";o.r(t);var l=function(){var e=this,t=e.$createElement,o=e._self._c||t;return o("div",{staticClass:"ele-body"},[o("el-card",{attrs:{shadow:"never"}},[o("el-form",{staticClass:"ele-form-search",attrs:{model:e.table.where,"label-width":"77px"},nativeOn:{keyup:function(t){return!t.type.indexOf("key")&&e._k(t.keyCode,"enter",13,t.key,"Enter")?null:e.$refs.table.reload()},submit:function(e){e.preventDefault()}}},[o("el-row",{attrs:{gutter:15}},[o("el-col",{attrs:{md:6,sm:12}},[o("el-form-item",{attrs:{label:"合作方向:"}},[o("el-input",{attrs:{placeholder:"请输入合作方向",clearable:""},model:{value:e.table.where.title,callback:function(t){e.$set(e.table.where,"title",t)},expression:"table.where.title"}})],1)],1),o("el-col",{attrs:{md:6,sm:12}},[o("el-form-item",{attrs:{label:"姓名:"}},[o("el-input",{attrs:{placeholder:"请输入姓名",clearable:""},model:{value:e.table.where.name,callback:function(t){e.$set(e.table.where,"name",t)},expression:"table.where.name"}})],1)],1),o("el-col",{attrs:{md:6,sm:12}},[o("div",{staticClass:"ele-form-actions"},[o("el-button",{staticClass:"ele-btn-icon",attrs:{type:"primary",icon:"el-icon-search"},on:{click:function(t){return e.$refs.table.reload()}}},[e._v("查询 ")]),o("el-button",{on:{click:function(t){(e.table.where={})&&e.$refs.table.reload()}}},[e._v("重置")])],1)])],1)],1),o("div",{staticClass:"ele-table-tool ele-table-tool-default"},[e.permission.includes("sys:work:add")?o("el-button",{staticClass:"ele-btn-icon",attrs:{type:"primary",icon:"el-icon-plus",size:"small"},on:{click:function(t){e.showEdit=!0}}},[e._v("添加 ")]):e._e(),e.permission.includes("sys:work:dall")?o("el-button",{staticClass:"ele-btn-icon",attrs:{type:"danger",icon:"el-icon-delete",size:"small"},on:{click:function(t){return e.remove()}}},[e._v("批量删除 ")]):e._e()],1),o("ele-data-table",{ref:"table",attrs:{config:e.table,choose:e.choose,height:"calc(100vh - 315px)","highlight-current-row":""},on:{"update:choose":function(t){e.choose=t}},scopedSlots:e._u([{key:"default",fn:function(t){var l=t.index;return[o("el-table-column",{attrs:{type:"selection",width:"45",align:"center",fixed:"left"}}),o("el-table-column",{attrs:{type:"index",index:l,label:"编号",width:"60",align:"center",fixed:"left","show-overflow-tooltip":""}}),o("el-table-column",{attrs:{prop:"title",label:"合作方向",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),o("el-table-column",{attrs:{prop:"content",label:"具体内容",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),o("el-table-column",{attrs:{prop:"name",label:"姓名",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),o("el-table-column",{attrs:{prop:"phone",label:"联系方式",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),o("el-table-column",{attrs:{label:"创建时间",sortable:"custom","show-overflow-tooltip":"","min-width":"160"},scopedSlots:e._u([{key:"default",fn:function(t){var o=t.row;return[e._v(e._s(e._f("toDateString")(1e3*o.create_time)))]}}],null,!0)}),o("el-table-column",{attrs:{label:"更新时间",sortable:"custom","show-overflow-tooltip":"","min-width":"160"},scopedSlots:e._u([{key:"default",fn:function(t){var o=t.row;return[e._v(e._s(e._f("toDateString")(1e3*o.update_time)))]}}],null,!0)}),o("el-table-column",{attrs:{label:"操作",width:"130px",align:"center",resizable:!1,fixed:"right"},scopedSlots:e._u([{key:"default",fn:function(t){var l=t.row;return[e.permission.includes("sys:work:edit")?o("el-link",{attrs:{icon:"el-icon-edit",type:"primary",underline:!1},on:{click:function(t){return e.edit(l)}}},[e._v("修改")]):e._e(),o("el-popconfirm",{staticClass:"ele-action",attrs:{title:"确定要删除此商务合作吗？"},on:{confirm:function(t){return e.remove(l)}}},[e.permission.includes("sys:work:delete")?o("el-link",{attrs:{slot:"reference",icon:"el-icon-delete",type:"danger",underline:!1},slot:"reference"},[e._v("删除")]):e._e()],1)]}}],null,!0)})]}}])})],1),o("el-dialog",{attrs:{title:(e.editForm.id,"修改商务合作"),visible:e.showEdit,width:"450px","destroy-on-close":!0,"lock-scroll":!1},on:{"update:visible":function(t){e.showEdit=t},closed:function(t){e.editForm={}}}},[o("el-form",{ref:"editForm",attrs:{model:e.editForm,rules:e.editRules,"label-width":"100px"}},[o("el-form-item",{attrs:{label:"合作方向：",prop:"title"}},[o("el-input",{attrs:{placeholder:"请输入合作方向",clearable:""},model:{value:e.editForm.title,callback:function(t){e.$set(e.editForm,"title",t)},expression:"editForm.title"}})],1),o("el-form-item",{attrs:{label:"具体内容：",prop:"content"}},[o("el-input-number",{staticClass:"ele-fluid ele-text-left",attrs:{"controls-position":"right",min:0,placeholder:"请输入具体内容"},model:{value:e.editForm.content,callback:function(t){e.$set(e.editForm,"content",t)},expression:"editForm.content"}})],1),o("el-form-item",{attrs:{label:"姓名：",prop:"name"}},[o("el-input",{attrs:{placeholder:"请输入姓名",clearable:""},model:{value:e.editForm.name,callback:function(t){e.$set(e.editForm,"name",t)},expression:"editForm.name"}})],1),o("el-form-item",{attrs:{label:"联系方式：",prop:"phone"}},[o("el-input",{attrs:{placeholder:"请输入联系方式",clearable:""},model:{value:e.editForm.phone,callback:function(t){e.$set(e.editForm,"phone",t)},expression:"editForm.phone"}})],1)],1),o("div",{attrs:{slot:"footer"},slot:"footer"},[o("el-button",{on:{click:function(t){e.showEdit=!1}}},[e._v("取消")]),o("el-button",{attrs:{type:"primary"},on:{click:e.save}},[e._v("保存")])],1)],1)],1)},r=[],a=(o("d81d"),o("5530")),s=o("2f62"),n={name:"SysWork",data:function(){return{table:{url:"/work/index",where:{}},choose:[],showEdit:!1,editForm:{},editRules:{title:[{required:!0,message:"请输入合作方向",trigger:"blur"}],content:[{required:!0,message:"请输入具体内容",trigger:"blur"}],name:[{required:!0,message:"请输入姓名",trigger:"blur"}],phone:[{required:!0,message:"请输入联系方式",trigger:"blur"}]}}},computed:Object(a["a"])({},Object(s["b"])(["permission"])),mounted:function(){},methods:{edit:function(e){var t=this;this.$http.get("/work/info?id="+e.id).then((function(e){0===e.data.code?(t.editForm=e.data.data,t.showEdit=!0):t.$message.error(e.data.msg)})).catch((function(e){t.$message.error(e.message)}))},save:function(){var e=this;this.$refs["editForm"].validate((function(t){if(!t)return!1;var o=e.$loading({lock:!0});e.$http.post("/work/edit",e.editForm).then((function(t){o.close(),0===t.data.code?(e.showEdit=!1,e.$message({type:"success",message:t.data.msg}),e.$refs.table.reload()):e.$message.error(t.data.msg)})).catch((function(t){o.close(),e.$message.error(t.message)}))}))},remove:function(e){var t=this;if(console.log(e),e){var o=this.$loading({lock:!0});this.$http.post("/work/delete",{id:e.id}).then((function(e){o.close(),0===e.data.code?(t.$message({type:"success",message:e.data.msg}),t.$refs.table.reload()):t.$message.error(e.data.msg)})).catch((function(e){o.close(),t.$message.error(e.message)}))}else{if(0===this.choose.length)return this.$message.error("请至少选择一条数据");var l=this.choose.map((function(e){return e.id}));this.$confirm("确定要删除选中的商务合作吗?","提示",{type:"warning"}).then((function(){var e=t.$loading({lock:!0});t.$http.post("/work/delete",{id:l}).then((function(o){e.close(),0===o.data.code?(t.$message({type:"success",message:o.data.msg}),t.$refs.table.reload()):t.$message.error(o.data.msg)})).catch((function(o){e.close(),t.$message.error(o.message)}))})).catch((function(){return 0}))}}}},i=n,c=(o("ecba"),o("2877")),d=Object(c["a"])(i,l,r,!1,null,"ca91107c",null);t["default"]=d.exports},ca1f:function(e,t,o){},ecba:function(e,t,o){"use strict";o("ca1f")}}]);