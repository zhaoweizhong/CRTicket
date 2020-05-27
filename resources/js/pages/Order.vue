<template>
    <div class="page">
        <el-table :data="orders" height="100%" stripe border class="order-table" v-loading="isOrdersLoading">
            <el-table-column width="70">
                <template slot="header">
                    订单号
                </template>
                <template slot-scope="scope">
                    {{scope.row.id}}
                </template>
            </el-table-column>
            <el-table-column width="70">
                <template slot="header">
                    车次
                </template>
                <template slot-scope="scope">
                    {{scope.row.tickets[0] ? JSON.parse(scope.row.tickets[0].train.numbers)[0] : ''}}
                </template>
            </el-table-column>
            <el-table-column width="90">
                <template slot="header">
                    <span>出发站</span>
                    <span>到达站</span>
                </template>
                <template slot-scope="scope">
                    <span>{{scope.row.tickets[0] ? scope.row.tickets[0].depart_station.name : ''}}</span>
                    <span>{{scope.row.tickets[0] ? scope.row.tickets[0].arrive_station.name : ''}}</span>
                </template>
            </el-table-column>
            <el-table-column width="80">
                <template slot="header">
                    <span>出发时间</span>
                    <span>到达时间</span>
                </template>
                <template slot-scope="scope">
                    <span>{{scope.row.tickets[0] ? $moment(scope.row.tickets[0].train.stations[scope.row.tickets[0].depart_station_num-1].pivot.depart_time, "HH:mm:ss").format('HH:mm') : ''}}</span>
                    <span>{{scope.row.tickets[0] ? $moment(scope.row.tickets[0].train.stations[scope.row.tickets[0].arrive_station_num-1].pivot.arrive_time, "HH:mm:ss").format('HH:mm') : ''}}</span>
                </template>
            </el-table-column>
            <el-table-column width="95">
                <template slot="header">
                    乘车日期
                </template>
                <template slot-scope="scope">
                    {{scope.row.tickets[0] ? scope.row.tickets[0].date : ''}}
                </template>
            </el-table-column>
            <el-table-column width="80">
                <template slot="header">
                    乘车人
                </template>
                <template slot-scope="scope">
                    {{scope.row.tickets[0] ? scope.row.tickets[0].passenger.name : ''}}
                </template>
            </el-table-column>
            <el-table-column width="50">
                <template slot="header">
                    车厢
                </template>
                <template slot-scope="scope">
                    {{scope.row.tickets[0] ? scope.row.tickets[0].carriage : ''}}
                </template>
            </el-table-column>
            <el-table-column width="70">
                <template slot="header">
                    座位号
                </template>
                <template slot-scope="scope">
                    {{scope.row.tickets[0] ? scope.row.tickets[0].seat : ''}}
                </template>
            </el-table-column>
            <el-table-column width="155">
                <template slot="header">
                    下单时间
                </template>
                <template slot-scope="scope">
                    {{scope.row.created_at ? $moment(scope.row.created_at).format('YYYY-MM-DD HH:mm:ss') : ''}}
                </template>
            </el-table-column>
            <el-table-column width="70">
                <template slot="header">
                    价格
                </template>
                <template slot-scope="scope">
                    ¥{{scope.row.tickets[0] ? scope.row.tickets[0].price : ''}}
                </template>
            </el-table-column>
            <el-table-column width="90">
                <template slot="header">
                    状态
                </template>
                <template slot-scope="scope">
                    <el-tag :type="scope.row.status_name[1]" size="medium">{{scope.row.status_name[0]}}</el-tag>
                </template>
            </el-table-column>
            <el-table-column fixed="right" width="100">
                <template slot="header">
                    操作
                </template>
                <template slot-scope="scope">
                    <el-button type="primary" size="mini" @click="openOrderDetailDialog(scope.row)" :disabled="scope.row.status == 'cancelled'">查看</el-button>
                </template>
            </el-table-column>
        </el-table>
        <el-dialog title="订单详情" :visible.sync="orderDetailDialogVisible" width="400px" class="order-detail-dialog">
            <el-table :data="orderDetail" height="100%" stripe border class="order-detail-table" header-cell-class-name="table-cell" cell-class-name="table-cell">
                <el-table-column prop="title" label="项目" width="179"></el-table-column>
                <el-table-column prop="data" label="内容" width="179"></el-table-column>
            </el-table>
            <div slot="footer" class="detail-dialog-footer">
                <el-button @click="orderDetailDialogVisible = false" :disabled="isOrderProcessing">关闭详情</el-button>
                <el-button v-if="orderDetail[12].data == '已支付'" type="danger" @click="cancelOrder(orderDetail[0].data)" :loading="isOrderProcessing">取消订单</el-button>
                <el-button v-if="orderDetail[12].data == '未支付'" type="success" @click="payOrder(orderDetail[0].data)" :loading="isOrderProcessing">支付订单</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
export default {
    name: 'Order',
    data() {
        return {
            orders: [],
            isOrdersLoading: true,
            orderDetailDialogVisible: false,
            orderDetail: [
                {
                    title: '订单号',
                    data: ''
                },
                {
                    title: '车次',
                    data: ''
                },
                {
                    title: '出发站',
                    data: ''
                },
                {
                    title: '到达站',
                    data: ''
                },
                {
                    title: '出发时间',
                    data: ''
                },
                {
                    title: '到达时间',
                    data: ''
                },
                {
                    title: '乘车日期',
                    data: ''
                },
                {
                    title: '乘车人',
                    data: ''
                },
                {
                    title: '车厢',
                    data: ''
                },
                {
                    title: '座位号',
                    data: ''
                },
                {
                    title: '下单时间',
                    data: ''
                },
                {
                    title: '价格',
                    data: ''
                },
                {
                    title: '状态',
                    data: ''
                },
            ],
            isOrderProcessing: false,
        }
    },
    beforeRouteEnter (to, from, next) {
        next(vm => {
            vm.$axios.get('/api/orders')
            .then(function (response) {
                for (let index = 0; index < response.data.length; index++) {
                    switch (response.data[index].status) {
                        case 'unpaid':
                            response.data[index].status_name = ['未支付', 'danger']
                            break;
                        case 'paid':
                            response.data[index].status_name = ['已支付', 'success']
                            break;
                        case 'cancelled':
                            response.data[index].status_name = ['已取消', 'warning']
                            break;
                    }
                }
                vm.orders = response.data
                vm.isOrdersLoading = false
            })
        })
    },
    created() {
        this.$emit('getTitle', '订单管理');
    },
    methods: {
        openOrderDetailDialog(order) {
            this.orderDetail[0].data = order.id
            this.orderDetail[1].data = JSON.parse(order.tickets[0].train.numbers)[0]
            this.orderDetail[2].data = order.tickets[0].depart_station.name
            this.orderDetail[3].data = order.tickets[0].arrive_station.name
            this.orderDetail[4].data = this.$moment(order.tickets[0].train.stations[order.tickets[0].depart_station_num-1].pivot.depart_time, "HH:mm:ss").format('HH:mm')
            this.orderDetail[5].data = this.$moment(order.tickets[0].train.stations[order.tickets[0].arrive_station_num-1].pivot.arrive_time, "HH:mm:ss").format('HH:mm')
            this.orderDetail[6].data = order.tickets[0].date
            this.orderDetail[7].data = order.tickets[0].passenger.name
            this.orderDetail[8].data = order.tickets[0].carriage
            this.orderDetail[9].data = order.tickets[0].seat
            this.orderDetail[10].data = this.$moment(order.created_at).format('YYYY-MM-DD HH:mm:ss')
            this.orderDetail[11].data = '¥' + order.tickets[0].price
            this.orderDetail[12].data = order.status_name[0]
            this.orderDetailDialogVisible = true
        },
        refreshTable() {
            this.isOrdersLoading = true
            var _t = this
            this.$axios.get('/api/orders')
                .then(function (response) {
                    for (let index = 0; index < response.data.length; index++) {
                        switch (response.data[index].status) {
                            case 'unpaid':
                                response.data[index].status_name = ['未支付', 'danger']
                                break;
                            case 'paid':
                                response.data[index].status_name = ['已支付', 'success']
                                break;
                            case 'cancelled':
                                response.data[index].status_name = ['已取消', 'warning']
                                break;
                        }
                    }
                    _t.orders = response.data
                    _t.isOrdersLoading = false
                })
        },
        cancelOrder(orderId) {
            this.isOrderProcessing = true
            this.$axios.delete('/api/orders/' + orderId).then((res) => {
                if (res.status == 200) {
                    this.isOrderProcessing = false
                    this.orderDetailDialogVisible = false
                    this.refreshTable()
                    this.$message({
                        showClose: true,
                        message: '订单取消成功',
                        type: 'success'
                    });
                } else {
                    this.isOrderProcessing = false
                    this.$message({
                        showClose: true,
                        message: '订单取消失败，请稍后重试',
                        type: 'error'
                    });
                }
            }).catch((error) => {
                this.$message({
                    showClose: true,
                    message: '订单取消失败：' + error.response.data.message,
                    type: 'error'
                });
                this.isOrderProcessing = false
            })
        },
        payOrder(orderId) {
            this.isOrderProcessing = true
            this.$axios.post('/api/orders/' + orderId + '/pay').then((res) => {
                const result = res.data
                if (res.status == 200) {
                    this.isOrderProcessing = false
                    this.orderDetailDialogVisible = false
                    this.refreshTable()
                    this.$message({
                        showClose: true,
                        message: '订单支付成功',
                        type: 'success'
                    });
                } else {
                    this.isOrderProcessing = false
                    this.$message({
                        showClose: true,
                        message: '订单支付失败，请稍后重试',
                        type: 'error'
                    });
                }
            }).catch((error) => {
                this.$message({
                    showClose: true,
                    message: '订单支付失败：' + error.response.data.message,
                    type: 'error'
                });
                this.isOrderProcessing = false
            })
        }
    },
}
</script>

<style lang="less" scoped>
.page {
    height: 100%;
}
.order-table {
    span {
        display: block;
    }
    .el-tag {
        width: 70px;
        font-size: 13px;
        margin: auto;
    }
}
</style>

<style lang="less">
.order-table {
    th, td {
        text-align: center;
    }
}
.order-detail-dialog {
    .el-dialog__body {
        padding-top: 10px;
        padding-bottom: 10px;
    }
    .detail-dialog-footer {
        text-align: center;
    }
}
.order-detail-table {
    .table-cell {
        padding: 8px 0;
    }
}
</style>