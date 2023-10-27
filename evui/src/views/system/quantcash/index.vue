<template>
	<div class="ele-body">
		<el-card shadow="never">
			<!-- 搜索表单 -->
			<el-form :model="table.where" label-width="77px" class="ele-form-search"
				@keyup.enter.native="$refs.table.reload()" @submit.native.prevent>
				<el-row :gutter="15">
					<el-col :md="6" :sm="12">
						<el-form-item label="审核状态:">
							<el-select v-model="table.where.is_check" placeholder="请选择审核状态" clearable class="ele-fluid">
								<el-option label="未审核" value="0" />
								<el-option label="已审核" value="1" />
								<el-option label="已拒绝" value="2" />
							</el-select>
						</el-form-item>
					</el-col>
					<el-col :md="6" :sm="12">
						<el-form-item label="提现用户:">
							<el-input v-model="table.where.user_name" placeholder="请输入提现用户" clearable />
						</el-form-item>
					</el-col>
					<el-col :md="9" :sm="19">
						<div class="block">
							<!-- <span class="demonstration">搜索时间</span> -->
							<el-form-item label="搜索时间">
								<el-date-picker @change="rq_change" value-format="yyyy-MM-dd HH:mm"
									style="margin-left:10px" v-model="create_time" type="datetimerange"
									start-placeholder="开始日期" end-placeholder="结束日期">
								</el-date-picker>
							</el-form-item>
						</div>
					</el-col>
					<el-col :md="6" :sm="12">
						<div class="ele-form-actions">
							<el-button type="success" @click="handleDownload()" icon="el-icon-download"
								class="ele-btn-icon">导出
							</el-button>
							<el-button type="primary" @click="$refs.table.reload()" icon="el-icon-search"
								class="ele-btn-icon">查询
							</el-button>
							<el-button @click=";(table.where = {}) && $refs.table.reload()">重置</el-button>
						</div>
					</el-col>
				</el-row>
			</el-form>
			<!-- 操作按钮 -->
			<div class="ele-table-tool ele-table-tool-default">
				<!--        <el-button @click="showEdit=true" type="primary" icon="el-icon-plus" class="ele-btn-icon" size="small" v-if="permission.includes('sys:quantcash:add')">添加-->
				<!--        </el-button>-->
				<el-button @click="remove()" type="danger" icon="el-icon-delete" class="ele-btn-icon" size="small"
					v-if="permission.includes('sys:quantcash:dall')">批量删除
				</el-button>
			</div>
			<!-- 数据表格 -->
			<ele-data-table ref="table" :config="table" :choose.sync="choose" height="calc(100vh - 315px)"
				highlight-current-row>
				<template slot-scope="{ index }">
					<el-table-column type="selection" width="45" align="center" fixed="left" />
					<el-table-column type="index" :index="index" label="编号" width="60" align="center" fixed="left"
						show-overflow-tooltip />

					<el-table-column prop="user_name" label="提现用户" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<el-table-column prop="amount" label="提现金额" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<el-table-column prop="fee" label="提现手续费" sortable="custom" show-overflow-tooltip min-width="120" />

					<el-table-column prop="chk_time" label="审核时间" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<el-table-column prop="is_check" label="审核状态" sortable="custom" :resizable="false" min-width="120">
						<template slot-scope="{ row }">
							<span v-if="row.is_check == 0" style="color: red">未审核</span>
							<span v-if="row.is_check == 1" style="color: green">已审核</span>
							<span v-if="row.is_check == 2" style="color: blue">已拒绝</span>
						</template>
					</el-table-column>

					<el-table-column prop="addr_cash" label="提现地址" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<el-table-column prop="txHash" label="交易哈希" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<el-table-column prop="create_time" label="创建时间" sortable="custom" show-overflow-tooltip
						min-width="120" />
					<el-table-column prop="update_time" label="更新时间" sortable="custom" show-overflow-tooltip
						min-width="120" />
					<!--          <el-table-column label="创建时间" sortable="custom" show-overflow-tooltip min-width="160">-->
					<!--            <template slot-scope="{row}">{{ row.create_time*1000 | toDateString }}</template>-->
					<!--          </el-table-column>-->
					<!--          <el-table-column label="更新时间" sortable="custom" show-overflow-tooltip min-width="160">-->
					<!--            <template slot-scope="{row}">{{ row.update_time*1000 | toDateString }}</template>-->
					<!--          </el-table-column>-->
					<el-table-column label="操作" width="130px" align="center" :resizable="false" fixed="right">
						<template slot-scope="{ row }">
							<el-link @click="edit(row)" icon="el-icon-edit" type="primary" :underline="false" v-if="
                  permission.includes('sys:quantcash:edit') && row.is_check == 0
                ">审核</el-link>
							<el-popconfirm title="确定要删除此提现记录吗？" @confirm="remove(row)" class="ele-action">
								<el-link slot="reference" icon="el-icon-delete" type="danger" :underline="false"
									v-if="permission.includes('sys:quantcash:delete')">删除</el-link>
							</el-popconfirm>
						</template>
					</el-table-column>
				</template>
			</ele-data-table>
		</el-card>
		<!-- 编辑弹窗 -->
		<el-dialog :title="editForm.id ? '修改提现记录' : '修改提现记录'" :visible.sync="showEdit" width="450px"
			@closed="editForm = {}" :destroy-on-close="true" :lock-scroll="false">
			<el-form :model="editForm" ref="editForm" :rules="editRules" label-width="100px">
				<el-form-item label="审核状态：" prop="is_check">
					<!--            <el-input-number v-model="editForm.is_check" controls-position="right" :min="0"-->
					<!--                              placeholder="请输入审核状态 0未审核1已审核2已拒绝" class="ele-fluid ele-text-left"/>-->

					<el-select v-model="editForm.is_check" placeholder="请选择审核状态" clearable class="ele-fluid">
						<el-option label="未审核" value="0" />
						<el-option label="已审核" value="1" />
						<el-option label="已拒绝" value="2" />
					</el-select>
				</el-form-item>
				<el-form-item prop="memo" label="拒绝备注" v-if="editForm.is_check == 2">
					<el-input placeholder="请填写备注" v-model="editForm.memo"></el-input>
				</el-form-item>
				<el-form-item prop="content" label="提现地址" v-if="editForm.is_check == 1">
					<el-input placeholder="提现地址" v-model="editForm.content"></el-input>
				</el-form-item>
			</el-form>
			<div slot="footer">
				<el-button @click="showEdit = false">取消</el-button>
				<el-button type="primary" @click="save">保存</el-button>
			</div>
		</el-dialog>
	</div>
</template>

<script>
	import {
		mapGetters
	} from 'vuex'
	export default {
		name: 'SysQuantCash',
		data() {
			return {
				export_list: [],
				count: "",
				create_time: '',
				table: {
					url: '/quantcash/index',
					where: {}
				}, // 表格配置
				choose: [], // 表格选中数据
				showEdit: false, // 是否显示表单弹窗
				editForm: {}, // 表单数据
				editRules: {
					// 表单验证规则
					memo: [{
						required: true,
						message: '请填写拒绝备注',
						trigger: 'blur'
					}],
					amount: [{
						required: true,
						message: '请输入提现金额',
						trigger: 'blur'
					}, ],

					fee: [{
						required: true,
						message: '请输入提现手续费',
						trigger: 'blur'
					}],

					chk_time: [{
						required: true,
						message: '请输入审核时间',
						trigger: 'blur'
					}, ],

					is_check: [{
						required: true,
						message: '请输入审核状态 0未审核1已审核2已拒绝',
						trigger: 'blur',
					}, ],

					addr_cash: [{
						required: true,
						message: '请输入提现地址',
						trigger: 'blur'
					}, ],

					txHash: [{
						required: true,
						message: '请输入交易哈希',
						trigger: 'blur'
					}, ],
				},
			}
		},
		computed: {
			...mapGetters(['permission']),
		},
		mounted() {
			this.account_details()
		},
		methods: {
			rq_change(e) {
				this.table.where.start_time = e[0]
				this.table.where.end_time = e[1]
			},
			/**
			 * 显示编辑
			 */
			edit(row) {
				this.$http
					.get('/quantcash/info?id=' + row.id)
					.then((res) => {
						if (res.data.code === 0) {
							this.editForm = res.data.data
							this.editForm.is_check = this.editForm.is_check + ''
							this.showEdit = true
						} else {
							this.$message.error(res.data.msg)
						}
					})
					.catch((e) => {
						this.$message.error(e.message)
					})
			},
			/**
			 * 保存编辑
			 */
			save() {
				this.$refs['editForm'].validate((valid) => {
					if (valid) {
						const loading = this.$loading({
							lock: true
						})
						this.$http
							.post('/quantcash/edit', this.editForm)
							.then((res) => {
								loading.close()
								if (res.data.code === 0) {
									this.showEdit = false
									this.$message({
										type: 'success',
										message: res.data.msg
									})
									this.$refs.table.reload()
								} else {
									this.$message.error(res.data.msg)
								}
							})
							.catch((e) => {
								loading.close()
								this.$message.error(e.message)
							})
					} else {
						return false
					}
				})
			},
			/**
			 * 刪除(批量刪除)
			 */
			remove(row) {
				console.log(row)
				if (!row) {
					// 批量删除
					if (this.choose.length === 0)
						return this.$message.error('请至少选择一条数据')
					let ids = this.choose.map((d) => d.id)
					this.$confirm('确定要删除选中的提现记录吗?', '提示', {
							type: 'warning',
						})
						.then(() => {
							const loading = this.$loading({
								lock: true
							})
							this.$http
								.post('/quantcash/delete', {
									id: ids
								})
								.then((res) => {
									loading.close()
									if (res.data.code === 0) {
										this.$message({
											type: 'success',
											message: res.data.msg
										})
										this.$refs.table.reload()
									} else {
										this.$message.error(res.data.msg)
									}
								})
								.catch((e) => {
									loading.close()
									this.$message.error(e.message)
								})
						})
						.catch(() => 0)
				} else {
					// 单个删除
					const loading = this.$loading({
						lock: true
					})
					this.$http
						.post('/quantcash/delete', {
							id: row.id
						})
						.then((res) => {
							loading.close()
							if (res.data.code === 0) {
								this.$message({
									type: 'success',
									message: res.data.msg
								})
								this.$refs.table.reload()
							} else {
								this.$message.error(res.data.msg)
							}
						})
						.catch((e) => {
							loading.close()
							this.$message.error(e.message)
						})
				}
			},
			account_details() {
				this.$http.get('/quantcash/index').then(res => {
					if (res.data.code === 0) {
						this.count = res.data.count
						console.log(this.count)
					} else {
						this.$message.error(res.data.msg);
					}
				}).catch(e => {
					this.$message.error(e.message);
				});

			},
			handleDownload(index) {
				this.table.where.limit = this.count
				this.table.where.page = 1
				this.$http.post('/quantcash/index', this.table.where).then(res => {
					if (res.data.code === 0) {
						this.export_list = res.data.data
						for (var i = 0; i < this.export_list.length; i++) {
							if (this.export_list[i].is_check == 1) {
								this.export_list[i].is_check = '已审核'
							} else if (this.export_list[i].is_check == 0) {
								this.export_list[i].is_check = '未审核'
							} else if (this.export_list[i].is_check == 2) {
								this.export_list[i].is_check = '已拒绝'
							}
							// this.export_list[i].is_check = getLocalTime(this.export_list[i]
							// 	.create_time);
							// this.export_list[i].update_time = getLocalTime(this.export_list[i]
							// 	.update_time);
						}
						console.log(this.export_list);
						import('@/vendor/Export2Excel').then(excel => {
							const header = [
								'提现用户',
								'提现金额',
								'提现手续费',
								'审核时间',
								'审核状态',
								'提现地址',
								'交易哈希',
								'创建时间',
								'更新时间',

							]
							const filterVal = [
								'user_name',
								'amount',
								'fee',
								'chk_time',
								'is_check',
								'addr_cash',
								'txHash',
								'create_time',
								'update_time',

							]
							const list = this.export_list
							const filename = '提现记录'
							const data = this.formatJson(filterVal, list)
							excel.export_json_to_excel({
								header,
								data,
								filename
							})
							this.downloadLoading = false
							this.downloadLoading1 = false
						})

					} else {
						this.$message.error(res.data.msg);
					}
				}).catch(e => {
					this.$message.error(e.message);
				});
			},
			formatJson(filterVal, jsonData) {
				return jsonData.map(v => filterVal.map(j => {
					if (j === 'timestamp') {
						return parseTime(v[j])
					} else {
						return v[j]
					}
				}))
			},
		},
	}

	function getLocalTime(nS) {
		return new Date(parseInt(nS) * 1000).toLocaleString().replace(/:\d{1,2}$/, ' ');
	}
</script>

<style scoped>
	.ele-block>>>.el-upload,
	.ele-block>>>.el-upload-dragger {
		width: 100%;
	}
</style>