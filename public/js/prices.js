const app = new Vue({
    el: '#app',
    data: {
        items: window.items,
        currentItem: {},
        test: '00',
        modal:  '',
        filter: '',
        disabled: false
    },
    computed: {
        itemssorted() {
            return Object.values(this.items).sort((item1, item2) => item1.name.toLowerCase() > item2.name.toLowerCase() ? 1 : -1)
        }
    },
    created() {
        this.freshCurrentItem()
    },
    mounted() {
        this.modal =  $('#changePrice')
        console.log(Object.values(this.items))
        console.log(typeof(Object.values(this.items)))
    },
    watch: {

    },
    methods: {
        addEdit(item) {
            let price = parseFloat(item.price.rub + '.' + item.price.kop)
            if (!item.name.length || !item.edizm.length || !price) {
                toastr.warning('Все поля должны быть заполнены')
                return
            }
            item.price = price
            this.disabled = true
            $.post('/changeprice', item, (r) => {
                console.log(r)
                if (r.success) {
                    toastr.success(r.success)
                    if (!item.id) {
                        item.id = r.id
                        this.items.push(item)
                    } else {
                        this.items.some(itm => {
                            if (itm.id == item.id) {
                                itm.name = item.name
                                itm.edizm = item.edizm
                                itm.price = item.price
                                return true
                            }
                        })
                    }
                    this.modal.modal('hide')
                } else {
                    toastr.error(r.error)
                }
            }).always(() => {
                this.disabled = false
            })
        },
        delitem(id) {
            this.disabled = true
            $.post('/delitem', {id: id}, (r) => {
                if (r.success) {
                    toastr.success('Удалено')
                    this.items.some((itm,i) => {
                        if (itm.id == id) {
                            this.items.splice(i,1)
                            return true
                        }
                    })
                } else {
                    toastr.warning(r.error)
                }
                this.modal.modal('hide')
            }).always(() => {
                this.disabled = false
            })
        },
        freshCurrentItem() {
            this.currentItem = {
                id: 0,
                name: '',
                edizm: '1кг',
                price: {
                    rub: 0,
                    kop: '00'
                }
            }
        },
        checkKop(event) {
            let t = this.currentItem.price.kop
            if ('0123456789'.indexOf(event.data) < 0) {
                this.currentItem.price.kop = t.replace(event.data, '')
                t = this.currentItem.price.kop
            }
            if (t.length == 0 || t.length > 3) {
                this.currentItem.price.kop = '00'
                setTimeout(() => {
                    event.target.setSelectionRange(0,0)
                }, 0)
            }
            else if (t.length == 1) {
                this.currentItem.price.kop += '0'
                setTimeout(() => {
                    event.target.setSelectionRange(1,1)
                }, 0)
            }
            else if (t.length == 3) {
                if (t.substr(2,1) == '0') {
                    this.currentItem.price.kop = t.substr(0,2)
                }
                else if (t.substr(0,1) == '0') {
                    this.currentItem.price.kop = t.substr(1)
                } 
                else if (t.substr(1,1) == '0') {
                    this.currentItem.price.kop = t.substr(0,1) + t.substr(2,1)
                }
                else {
                    this.currentItem.price.kop = t.substr(1,2)
                }
            }
        },
        focusinput(event) {
            event.target.setSelectionRange(0,event.target.value.length)
        },
        onlydigits(event) {
            let t = this.currentItem.price.rub
            if ('0123456789'.indexOf(event.data) < 0) {
                this.currentItem.price.rub = t.replace(event.data, '')
                t = this.currentItem.price.rub
            }
            if (t.length) {
                this.currentItem.price.rub = parseInt(t)
            }
            else if (!t.length) {
                this.currentItem.price.rub = '0'
            }
        },
        openmodal() {
            this.freshCurrentItem()
            this.modal.modal('show')
        },
        openitem(item) {
            let p = this.rubkop(item.price).split(',')
            this.currentItem = {
                name: item.name,
                id: item.id,
                edizm: item.edizm,
                price: {
                    rub: p[0],
                    kop: p[1]
                }
            }
            this.modal.modal('show')
        },
        rubkop(n) {
            let p = n.toString().split('.')
            let k = !p[1] ? '00' : p[1].length == 1 ? p[1] + '0' : p[1]
            return p[0] + ',' + k
        }
    }
})
