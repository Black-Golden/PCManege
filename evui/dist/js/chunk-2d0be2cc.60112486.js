(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-2d0be2cc"],{"2ead":function(e,t,o){"use strict";o.r(t);var s=function(){var e=this,t=e.$createElement,o=e._self._c||t;return o("div",{staticClass:"dict-data-page"},[o("el-form",{staticClass:"ele-form-search ele-table-tool-default",attrs:{model:e.table.where,size:"small"},nativeOn:{keyup:function(t){return!t.type.indexOf("key")&&e._k(t.keyCode,"enter",13,t.key,"Enter")?null:e.$refs.table.reload()},submit:function(e){e.preventDefault()}}},[o("el-row",{attrs:{gutter:15}},[o("el-col",{attrs:{md:6,sm:12}},[o("el-form-item",[o("el-input",{attrs:{placeholder:"请输入字典项名称",clearable:"",size:"small"},model:{value:e.table.where.name,callback:function(t){e.$set(e.table.where,"name",t)},expression:"table.where.name"}})],1)],1),o("el-col",{attrs:{md:6,sm:12}},[o("el-form-item",[o("el-input",{attrs:{placeholder:"请输入字典项值",clearable:"",size:"small"},model:{value:e.table.where.code,callback:function(t){e.$set(e.table.where,"code",t)},expression:"table.where.code"}})],1)],1),o("el-col",{attrs:{md:12,sm:12}},[o("div",{staticClass:"ele-form-actions"},[o("el-button",{staticClass:"ele-btn-icon",attrs:{type:"primary",icon:"el-icon-search",size:"small"},on:{click:function(t){return e.$refs.table.reload()}}},[e._v("查询 ")]),e.permission.includes("sys:dictionary:add")?o("el-button",{staticClass:"ele-btn-icon",attrs:{type:"primary",icon:"el-icon-plus",size:"small"},on:{click:function(t){e.showEdit=!0}}},[e._v("添加 ")]):e._e(),e.permission.includes("sys:dictionary:delete")?o("el-button",{staticClass:"ele-btn-icon",attrs:{type:"danger",icon:"el-icon-delete",size:"small"},on:{click:function(t){return e.remove()}}},[e._v("删除 ")]):e._e()],1)])],1)],1),o("ele-data-table",{ref:"table",attrs:{config:e.table,choose:e.choose,height:e.tbHeight,"highlight-current-row":""},on:{"update:choose":function(t){e.choose=t}}},[o("el-table-column",{attrs:{type:"selection",width:"45",align:"center"}}),o("el-table-column",{attrs:{prop:"name",label:"字典项名称",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),o("el-table-column",{attrs:{prop:"code",label:"字典项值",sortable:"custom","show-overflow-tooltip":"","min-width":"110"}}),o("el-table-column",{attrs:{prop:"sort",label:"排序号",sortable:"custom",width:"90px"}}),o("el-table-column",{attrs:{label:"创建时间",sortable:"custom","show-overflow-tooltip":"","min-width":"110"},scopedSlots:e._u([{key:"default",fn:function(t){var o=t.row;return[e._v(e._s(e._f("toDateString")(1e3*o.create_time)))]}}])}),o("el-table-column",{attrs:{label:"操作",width:"130px",align:"center",resizable:!1,fixed:"right"},scopedSlots:e._u([{key:"default",fn:function(t){var s=t.row;return[e.permission.includes("sys:dictionary:edit")?o("el-link",{attrs:{icon:"el-icon-edit",type:"primary",underline:!1},on:{click:function(t){return e.edit(s)}}},[e._v("修改 ")]):e._e(),o("el-popconfirm",{staticClass:"ele-action",attrs:{title:"确定要删除此字典项吗？"},on:{confirm:function(t){return e.remove(s)}}},[e.permission.includes("sys:dictionary:delete")?o("el-link",{attrs:{slot:"reference",icon:"el-icon-delete",type:"danger",underline:!1},slot:"reference"},[e._v("删除 ")]):e._e()],1)]}}])})],1),o("el-dialog",{attrs:{title:e.editForm.id?"修改字典项":"添加字典项",visible:e.showEdit,width:"400px","destroy-on-close":!0,"lock-scroll":!1},on:{"update:visible":function(t){e.showEdit=t},closed:function(t){e.editForm={}}}},[o("el-form",{ref:"editForm",attrs:{model:e.editForm,rules:e.editRules,"label-width":"96px"}},[o("el-form-item",{attrs:{label:"字典项名称:",prop:"name"}},[o("el-input",{attrs:{placeholder:"请输入字典项名称",clearable:""},model:{value:e.editForm.name,callback:function(t){e.$set(e.editForm,"name",t)},expression:"editForm.name"}})],1),o("el-form-item",{attrs:{label:"字典项值:",prop:"code"}},[o("el-input",{attrs:{placeholder:"请输入字典项值",clearable:""},model:{value:e.editForm.code,callback:function(t){e.$set(e.editForm,"code",t)},expression:"editForm.code"}})],1),o("el-form-item",{attrs:{label:"排序号:",prop:"sort"}},[o("el-input-number",{staticClass:"ele-fluid ele-text-left",attrs:{"controls-position":"right",min:0,placeholder:"请输入排序号"},model:{value:e.editForm.sort,callback:function(t){e.$set(e.editForm,"sort",t)},expression:"editForm.sort"}})],1),o("el-form-item",{attrs:{label:"备注:"}},[o("el-input",{attrs:{placeholder:"请输入备注",rows:4,type:"textarea"},model:{value:e.editForm.note,callback:function(t){e.$set(e.editForm,"note",t)},expression:"editForm.note"}})],1)],1),o("div",{attrs:{slot:"footer"},slot:"footer"},[o("el-button",{on:{click:function(t){e.showEdit=!1}}},[e._v("取消")]),o("el-button",{attrs:{type:"primary"},on:{click:e.save}},[e._v("保存")])],1)],1)],1)},l=[],r=(o("d81d"),o("a9e3"),o("5530")),i=o("2f62"),a={name:"SysDictData",props:{dictId:Number,tbHeight:String},data:function(){return{table:{url:"/dictdata/index",where:{dictId:this.dictId},page:!0},choose:[],showEdit:!1,editForm:{},editRules:{name:[{required:!0,message:"请输入字典项名称",trigger:"blur"}],code:[{required:!0,message:"请输入字典项值",trigger:"blur"}],sort:[{required:!0,message:"请输入排序号",trigger:"blur"}]}}},computed:Object(r["a"])({},Object(i["b"])(["permission"])),methods:{edit:function(e){this.editForm=Object.assign({},e),this.showEdit=!0},save:function(){var e=this;this.$refs["editForm"].validate((function(t){if(!t)return!1;var o=e.$loading({lock:!0});e.editForm=Object.assign({},e.editForm,{dict_id:e.dictId}),e.$http.post("/dictdata/edit",e.editForm).then((function(t){o.close(),0===t.data.code?(e.showEdit=!1,e.$message({type:"success",message:t.data.msg}),e.$refs.table.reload()):e.$message.error(t.data.msg)})).catch((function(t){o.close(),e.$message.error(t.message)}))}))},remove:function(e){var t=this;if(e){var o=this.$loading({lock:!0});this.$http.post("/dictdata/delete",{id:e.id}).then((function(e){o.close(),0===e.data.code?(t.$message({type:"success",message:e.data.msg}),t.$refs.table.reload()):t.$message.error(e.data.msg)})).catch((function(e){o.close(),t.$message.error(e.message)}))}else{if(0===this.choose.length)return this.$message.error("请至少选择一条数据");var s=this.choose.map((function(e){return e.id}));this.$confirm("确定要删除选中的字典项吗?","提示",{type:"warning"}).then((function(){var e=t.$loading({lock:!0});t.$http.post("/dictdata/delete",{id:s}).then((function(o){e.close(),0===o.data.code?(t.$message({type:"success",message:o.data.msg}),t.$refs.table.reload()):t.$message.error(o.data.msg)})).catch((function(o){e.close(),t.$message.error(o.message)}))})).catch((function(){return 0}))}}},watch:{dictId:function(){this.table.where={dictId:this.dictId},this.$refs.table.reload()}}},n=a,c=o("2877"),d=Object(c["a"])(n,s,l,!1,null,"6d164678",null);t["default"]=d.exports}}]);