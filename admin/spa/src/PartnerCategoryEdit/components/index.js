import React from 'react';
import {connect} from 'react-redux';
import {Redirect, withRouter} from 'react-router-dom';
import {FETCH_SUCCESS, MODEL_CHANGED} from '../actions';
import {OrderTypes} from '../../CategoryEdit/components';
import selectors from './selectors';
import SaveCategory from '../actions/SaveCategory';
import FetchUnits from '../../Unit/actions/FetchItems';
import FetchItem from '../actions/FetchItem';
import translator from '../../translations/translator';
import {setTitle} from "../../Common/utils";
import DeleteCategory from "../actions/DeleteCategory";

class PartnerCategoryEdit extends React.Component {

    state = {
        canRedirect: false
    }

    componentWillMount() {

        this.props.dispatch(FetchUnits(null, 1, 0))

        const {id} = this.props.match.params
        if (id > 0) {

            setTitle(translator('loading'))

            this.props.dispatch(FetchItem(id))
        } else {

            setTitle(translator('navigation_categories_new'))

            this.props.dispatch({
                type: FETCH_SUCCESS,
                payload: {}
            })
        }
    }

    remove = () => {
        if (!confirm(translator('confirm_delete'))) return

        const {model} = this.props.PartnerCategoryEdit

        this.props.dispatch(DeleteCategory(model, () => {
            this.setState({
                canRedirect: true
            })
        }))
    }

    submit = () => {
        const {model} = this.props.PartnerCategoryEdit

        this.props.dispatch(SaveCategory(model, () => {
            this.setState({
                canRedirect: true
            })
        }))
    }

    change = (key, value) => this.props.dispatch({
        type: MODEL_CHANGED,
        payload: {
            [key]: value
        }
    })

    changePrice = e => {
        let float = e.target.value.replace(/[^0-9.]/g, '')
        let value = parseFloat(float)
        if (isNaN(value) || float === '') value = null;

        this.change('price', value)
    }

    changeBidirectional = e => {
        const value = e.target.checked
        console.log('bidirectional: '+value)
        this.change('bidirectional',value)
    }

    changeInt = name => e => {
        let int = e.target.value.replace(/[^0-9]/g, '')
        let value = parseInt(int)
        if (isNaN(value) || int === '') value = 0;

        this.change(name, value)
    }

    changeCategory = e => {
        let value = parseInt(e.target.value.replace(/[^0-9]/g, ''))
        if (isNaN(value)) value = 0;

        const item = this.props.Category.items.find(item => item.id === value)

        this.change('category', item)
    }

    changeUnit = e => {
        let value = parseInt(e.target.value.replace(/[^0-9]/g, ''))
        if (isNaN(value)) value = 0;

        const item = this.props.Unit.items.find(item => item.id === value)

        this.change('unit', item)
    }

    changeString = name => e => this.change(name, e.target.value)

    getError = key => {
        const {errors} = this.props.PartnerCategoryEdit.validator

        if (errors[key] === undefined) return null

        return <small className="d-block c-red-500 form-text text-muted">{errors[key]}</small>
    }

    render() {

        const {model, isValid, isLoading, isSaveSuccess, serverErrors} = this.props.PartnerCategoryEdit
        const units = this.props.Unit.items
        const categories = [...this.props.Category.items]

        if (this.state.canRedirect) {
            return <Redirect to="/categories"/>
        }

        if (model.category) {
            const currentCategory = categories.find(item => item.id === model.category)
            if (!currentCategory) {
                categories.push(model.category)
            }
        }

        if (model.id && model.category) {
            setTitle('#' + model.id + ' ' + model.category.name)
        }

        const isRequired = model.price !== null || model.unit !== null || model.minAmount !== null

        return <div className="card my-3">

            <div className="card-header">
                <div className="row">
                    <div className="col">
                        <h4 className="m-0">
                            {translator('navigation_categories')}&nbsp;/&nbsp;
                            {model.id > 0
                                ? <span>#{model.id}&nbsp;{model.name}</span>
                                : <span>{translator('create')}</span>}
                        </h4>
                    </div>
                    <div className="col text-right">
                        {model.id && <button className="btn btn-danger btn-sm mr-2"
                                             disabled={isLoading}
                                             onClick={this.remove}>
                            <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-times"}/>
                            &nbsp;{translator('remove')}
                        </button>}

                        <button className="btn btn-success btn-sm"
                                disabled={!isValid || isLoading}
                                onClick={this.submit}>
                            <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-check"}/>
                            &nbsp;{translator('save')}
                        </button>

                        {isSaveSuccess && <div className="text-muted c-green-500">
                            <i className="fa fa-check"/>&nbsp;{translator('save_success_alert')}
                        </div>}
                    </div>
                </div>
            </div>

            <div className="card-body">
                <div className="row">
                    <div className="col">
                        {serverErrors.length > 0 && <div className="alert alert-danger">
                            <ul className="simple">{serverErrors.map((e, i) => <li key={i}>{e}</li>)}</ul>
                        </div>}

                        <div className="form-group">
                            <label className="required">{translator('type')}</label>
                            <select name="type"
                                    className="form-control"
                                    onChange={this.changeString('type')}
                                    disabled={!!model.id}
                                    value={model.type || 0}>
                                <option value={0} disabled={true}>{translator('select_value')}</option>
                                {OrderTypes.map((type, i) =>
                                    <option key={i} value={type.value}>{type.label}</option>
                                )}
                            </select>
                            {this.getError('type')}
                        </div>

                        <div className="form-group">
                            <label className="required">{translator('category')}</label>
                            <select name="category"
                                    className="form-control"
                                    disabled={!model.type}
                                    onChange={this.changeCategory}
                                    value={model.category ? model.category.id : 0}>
                                <option value={0} disabled={true}>{translator('select_value')}</option>
                                {categories.map((item, i) => {
                                    let lvl = ''
                                    for (let i = 0; i < item.lvl; i++) {
                                        lvl += ' - '
                                    }

                                    return <option
                                        key={i}
                                        value={item.id}
                                        disabled={item.children && item.children.length > 0}>
                                        {lvl}{item.name}
                                    </option>
                                })}
                            </select>
                            {this.getError('category')}
                        </div>

                        {!model.hasChildren && <div className="form-group">
                            <label className={isRequired ? "required" : ""}>{translator('unit')}</label>
                            <select name="unit"
                                    className="form-control"
                                    onChange={this.changeUnit}
                                    value={model.unit ? model.unit.id : 0}>
                                <option value={0} disabled={true}>{translator('select_value')}</option>
                                {units.map((item, i) =>
                                    <option key={i} value={item.id}>{item.name}</option>
                                )}
                            </select>
                            {this.getError('unit')}
                        </div>}

                        {!model.hasChildren && <div className="form-group">
                            <label className={isRequired ? "required" : ""}>{translator('min_amount')}</label>
                            <input type="number"
                                   name="minAmount"
                                   className="form-control"
                                   onChange={this.changeInt('minAmount')}
                                   value={model.minAmount || ''}/>
                            {this.getError('minAmount')}
                        </div>}

                        {!model.hasChildren && <div className="form-group">
                            <label className={isRequired ? "required" : ""}>{translator('price')}</label>
                            <input type="number"
                                   name="price"
                                   min={0}
                                   step={0.01}
                                   className="form-control"
                                   onChange={this.changePrice}
                                   value={model.price !== null ? model.price : ''}/>
                            {this.getError('price')}
                        </div>}

                        {(!model.hasChildren && ["recycling", "busybee"].includes(model.type)) 
                            && <div className="form-check">
                            <input type="checkbox"
                                   name="bidirectional"
                                   className="form-check-input"
                                   onChange={this.changeBidirectional}
                                   checked={!!model.bidirectional}/>
                            <label>{translator('partner_pays')}</label>
                        </div>}
                    </div>
                </div>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(PartnerCategoryEdit))
