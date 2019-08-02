const app = new Vue({
    el: '#app',
    data: {
        items: Object.values(window.items).sort((item1, item2) => item1.name.toLowerCase() > item2.name.toLowerCase() ? 1 : -1),
        modal:  '',
        filter: '',
        disabled: false,
        newIng: '',
        products: [],
        currentProduct: {},
        defaultMarja: window.defaultMarja,
        newDefMarja: window.defaultMarja
    },
    computed: {
        currentCostPrice() {
            if (!Object.keys(this.currentProduct).length) return {}
            return this.getCostPrice(this.currentProduct)
        },
        productssorted() {
            return this.products.sort((p1, p2) => p1.name.toLowerCase() > p2.name.toLowerCase() ? 1 : -1)
        }
    },
    created() {
        this.freshCurrentProduct()
        this.products = window.products.map(p => {
            p.ings = JSON.parse(p.ingridients).map(ing => {
                this.items.some(item => {
                    if (item.id == ing.ingId) {
                        ing.ing = item
                        return true
                    }
                })
                return ing
            })
            p.useDefMarja = p.marja == 'default' ? true : false
            p.marja = p.marja == 'default' ? this.defaultMarja : p.marja
            return this.getCostPrice(p)
        })
    },
    mounted() {
        this.modal =  $('#changePrice')
    },
    watch: {
        newIng(i) {
            if (i.length == 0) return
            let hasItem = this.currentProduct.ings.some(ing => {
                if (ing.ing.id == this.items[i].id) {
                    toastr.warning('Такой ингридиент уже есть')
                    return true
                }
            })
            if (!hasItem) {
                this.currentProduct.ings.push({ing: this.items[i], kol: 0})
            }
            this.newIng = ''
        }
    },
    methods: {
        getCostPerIng(ing) {
            let isEnergy = ing.ing.name == 'Электроэнергия' ? 1 : 1000
            return Math.round((ing.ing.price * ing.kol * 10)/isEnergy) / 10
        },
        getCostPrice(p) {
            p.allCost = 0
            if (!('marja' in p) || p.useDefMarja) {
                p.marja = this.defaultMarja
            }
            p.ings.forEach(ing => {
                p.allCost += this.getCostPerIng(ing)
            })
            p.allCost = Math.round(p.allCost*10)/10
            p.costPerOne = p.batch ? Math.round((p.allCost / p.batch)*10)/10 : '-'
            p.price = p.batch ? Math.round(p.costPerOne * (1 + p.marja/100)) : '-'
            return p
        },
        addEdit(p) {
            if (!p.name.length || !p.ings.length) {
                toastr.warning('Все поля должны быть заполнены')
                return
            }
            this.disabled = true
            let req = {
                id: p.id,
                name: p.name,
                batch: p.batch,
                marja: p.useDefMarja ? 'default' : p.marja,
                ingridients: p.ings.map(ing => {
                    return {
                        ingId: ing.ing.id,
                        kol: ing.kol
                    }
                })
            }
            req.ingridients = JSON.stringify(req.ingridients)
            $.post('/changeproduct', req, (r) => {
                if (r.success) {
                    toastr.success(r.success)
                    if (!p.id) {
                        p.id = r.id
                        this.products.push(p)
                    } else {
                        this.products.some((prod,num) => {
                            if (prod.id == r.id) {
                                Vue.set(this.products, num, p)
                                return  true
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
        delproduct(id) {
            this.disabled = true
            $.post('/delproduct', {id: id}, (r) => {
                if (r.success) {
                    toastr.success('Удалено')
                    this.products.some((p,i) => {
                        if (p.id == id) {
                            this.products.splice(i,1)
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
        freshCurrentProduct() {
            this.currentProduct = {
                id: 0,
                name: '',
                batch: 1,
                ings: [],
                marja: this.defaultMarja,
                useDefMarja: true
            }
        },
        focusinput(event) {
            event.target.setSelectionRange(0,event.target.value.length)
        },
        openmodal() {
            this.freshCurrentProduct()
            this.modal.modal('show')
        },
        openproduct(p) {
            this.currentProduct = JSON.parse(JSON.stringify(p))
            this.modal.modal('show')
        },
        onlydigitskol(event, i) {
            let t = this.currentProduct.ings[i].kol
            if ('0123456789'.indexOf(event.data) < 0) {
                this.currentProduct.ings[i].kol = t.replace(event.data, '')
                t = this.currentProduct.ings[i].kol
            }
            if (t.length) {
                this.currentProduct.ings[i].kol = parseInt(t)
            }
            else if (!t.length) {
                this.currentProduct.ings[i].kol = '0'
            }
        },
        onlydigitsbatch(event) {
            let t = this.currentProduct.batch
            if ('0123456789'.indexOf(event.data) < 0) {
                this.currentProduct.batch = t.replace(event.data, '')
                t = this.currentProduct.batch
            }
            if (t.length) {
                this.currentProduct.batch = parseInt(t)
            }
            else if (!t.length) {
                this.currentProduct.batch = 1
            }
        },
        onlypercent(event) {
            let t = this.currentProduct.marja
            if ('0123456789'.indexOf(event.data) < 0) {
                this.currentProduct.marja = t.replace(event.data, '')
                t = this.currentProduct.marja
            }
            if (t.length) {
                this.currentProduct.marja = parseInt(t)
            }
            else if (!t.length) {
                this.currentProduct.marja = this.defaultMarja
            }
        },
        onlypercentnewdefmarja(event) {
            let t = this.newDefMarja
            if ('0123456789'.indexOf(event.data) < 0) {
                this.newDefMarja = t.replace(event.data, '')
                t = this.newDefMarja
            }
            if (t.length) {
                this.newDefMarja = parseInt(t)
            }
            else if (!t.length) {
                this.newDefMarja = this.defaultMarja
            }
        },
        saveNewDefMarja() {
            this.disabled = true
            $.post('/saveNewDefMarja', {marja: this.newDefMarja}, r => {
                if (r.success) {
                    toastr.success('Наценка сохранена')
                    this.defaultMarja = this.newDefMarja
                    this.products = this.products.map(p => {
                        if (p.useDefMarja) {
                            p.marja = this.defaultMarja
                            return this.getCostPrice(p)
                        }
                        return p
                    })
                } else {
                    toastr.warning(r.error)
                }
            }).always(() => {
                this.disabled = false
            })
        }
    }
})