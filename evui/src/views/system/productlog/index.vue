<template>
	<div class="ele-body">
		<el-card shadow="never">
			<!-- 搜索表单 -->
			<el-form :model="table.where" label-width="77px" class="ele-form-search"
				@keyup.enter.native="$refs.table.reload()" @submit.native.prevent>
				<el-row :gutter="15">
					<el-col :md="6" :sm="12">
						<div class="ele-form-actions">
							<el-button type="success" @click="handleDownload()" icon="el-icon-download"
								class="ele-btn-icon">导出
							</el-button>
						</div>
					</el-col>
				</el-row>
			</el-form>
			<!-- 操作按钮 -->
			<div class="ele-table-tool ele-table-tool-default">
				<!-- <el-button @click="showEdit=true" type="primary" icon="el-icon-plus" class="ele-btn-icon" size="small"
					v-if="permission.includes('sys:productlog:add')">添加
				</el-button> -->
				<!-- <el-button @click="remove()" type="danger" icon="el-icon-delete" class="ele-btn-icon" size="small"
					v-if="permission.includes('sys:productlog:dall')">批量删除
				</el-button> -->
			</div>
			<!-- 数据表格 -->
			<ele-data-table ref="table" :config="table" :choose.sync="choose" height="calc(100vh - 315px)"
				highlight-current-row>
				<template slot-scope="{index}">
					<el-table-column type="selection" width="45" align="center" fixed="left" />
					<el-table-column type="index" :index="index" label="编号" width="60" align="center" fixed="left"
						show-overflow-tooltip />

					<el-table-column prop="user" label="用户名称" sortable="custom" show-overflow-tooltip min-width="120" />

					<el-table-column prop="product_name" label="产品名称" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<!-- <el-table-column prop="order_id" label="策略id" sortable="custom" show-overflow-tooltip
						min-width="120" /> -->

					<el-table-column prop="last_money" label="当前金额" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<el-table-column prop="win_money" label="收益" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<el-table-column label="创建时间" sortable="custom" show-overflow-tooltip min-width="160">
						<template slot-scope="{row}">{{ row.create_time }}</template>
					</el-table-column>
					<el-table-column label="更新时间" sortable="custom" show-overflow-tooltip min-width="160">
						<template slot-scope="{row}">{{ row.update_time*1000 | toDateString }}</template>
					</el-table-column>
					
				</template>
			</ele-data-table>
		</el-card>
		<!-- 编辑弹窗 -->
		<el-dialog :title="editForm.id?'修改累计收益':'修改累计收益'" :visible.sync="showEdit" width="450px" @closed="editForm={}"
			:destroy-on-close="true" :lock-scroll="false">
			<el-form :model="editForm" ref="editForm" :rules="editRules" label-width="100px">

				<el-form-item label="用户id：" prop="user_id">
					<el-input-number v-model="editForm.user_id" controls-position="right" :min="0" placeholder="请输入用户id"
						class="ele-fluid ele-text-left" />
				</el-form-item>

				<el-form-item label="产品id：" prop="product_id">
					<el-input-number v-model="editForm.product_id" controls-position="right" :min="0"
						placeholder="请输入产品id" class="ele-fluid ele-text-left" />
				</el-form-item>

				<el-form-item label="策略id：" prop="order_id">
					<el-input-number v-model="editForm.order_id" controls-position="right" :min="0"
						placeholder="请输入策略id" class="ele-fluid ele-text-left" />
				</el-form-item>

				<el-form-item label="当前金额：" prop="last_money">
					<el-input-number v-model="editForm.last_money" controls-position="right" :min="0"
						placeholder="请输入当前金额" class="ele-fluid ele-text-left" />
				</el-form-item>

				<el-form-item label="收益：" prop="win_money">
					<el-input-number v-model="editForm.win_money" controls-position="right" :min="0" placeholder="请输入收益"
						class="ele-fluid ele-text-left" />
				</el-form-item>

			</el-form>
			<div slot="footer">
				<el-button @click="showEdit=false">取消</el-button>
				<el-button type="primary" @click="save">保存</el-button>
			</div>
		</el-dialog>
	</div>
</template>

<script>
	import {
		mapGetters
	} from "vuex";
	export default {
		name: "SysProductLog",
		data() {
			return {
				export_list: [],
				count: "",
				table: {
					url: '/productlog/index',
					where: {}
				}, // 表格配置
				choose: [], // 表格选中数据
				showEdit: false, // 是否显示表单弹窗
				editForm: {}, // 表单数据
				editRules: { // 表单验证规则

					user_id: [{
						required: true,
						message: '请输入用户id',
						trigger: 'blur'
					}],

					product_id: [{
						required: true,
						message: '请输入产品id',
						trigger: 'blur'
					}],

					order_id: [{
						required: true,
						message: '请输入策略id',
						trigger: 'blur'
					}],

					last_money: [{
						required: true,
						message: '请输入当前金额',
						trigger: 'blur'
					}],

					win_money: [{
						required: true,
						message: '请输入收益',
						trigger: 'blur'
					}],

				},
			}
		},
		computed: {
			...mapGetters(["permission"]),
		},
		mounted() {
			this.account_details()
		},
		methods: {
			/**
			 * 显示编辑
			 */
			edit(row) {
				this.$http.get('/productlog/info?id=' + row.id).then(res => {
					if (res.data.code === 0) {
						this.editForm = res.data.data;
						this.showEdit = true;
					} else {
						this.$message.error(res.data.msg);
					}
				}).catch(e => {
					this.$message.error(e.message);
				});
			},
			/**
			 * 保存编辑
			 */
			save() {
				this.$refs['editForm'].validate((valid) => {
					if (valid) {
						const loading = this.$loading({
							lock: true
						});
						this.$http.post('/productlog/edit', this.editForm).then(res => {
							loading.close();
							if (res.data.code === 0) {
								this.showEdit = false;
								this.$message({
									type: 'success',
									message: res.data.msg
								});
								this.$refs.table.reload();
							} else {
								this.$message.error(res.data.msg);
							}
						}).catch(e => {
							loading.close();
							this.$message.error(e.message);
						});
					} else {
						return false;
					}
				});
			},
			/**
			 * 刪除(批量刪除)
			 */
			remove(row) {
				console.log(row)
				if (!row) { // 批量删除
					if (this.choose.length === 0) return this.$message.error('请至少选择一条数据');
					let ids = this.choose.map(d => d.id);
					this.$confirm('确定要删除选中的累计收益吗?', '提示', {
						type: 'warning'
					}).then(() => {
						const loading = this.$loading({
							lock: true
						});
						this.$http.post('/productlog/delete', {
							id: ids
						}).then(res => {
							loading.close();
							if (res.data.code === 0) {
								this.$message({
									type: 'success',
									message: res.data.msg
								});
								this.$refs.table.reload();
							} else {
								this.$message.error(res.data.msg);
							}
						}).catch(e => {
							loading.close();
							this.$message.error(e.message);
						});
					}).catch(() => 0);
				} else { // 单个删除
					const loading = this.$loading({
						lock: true
					});
					this.$http.post('/productlog/delete', {
						id: row.id
					}).then(res => {
						loading.close();
						if (res.data.code === 0) {
							this.$message({
								type: 'success',
								message: res.data.msg
							});
							this.$refs.table.reload();
						} else {
							this.$message.error(res.data.msg);
						}
					}).catch(e => {
						loading.close();
						this.$message.error(e.message);
					});
				}
			},
			account_details() {
				this.$http.get('/productlog/index').then(res => {
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
				this.$http.post('/productlog/index', this.table.where).then(res => {
					if (res.data.code === 0) {
						this.export_list = res.data.data
						
						console.log(this.export_list);
						import('@/vendor/Export2Excel').then(excel => {
							const header = [
								'用户名称',
								'产品名称',
								'当前金额',
								'收益',

								'创建时间',
								'更新时间'


							]
							const filterVal = [
								'user',
								'product_name',
								'last_money',
								'win_money',

								'create_time',
								'update_time',


							]
							const list = this.export_list
							const filename = '累计收益'
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