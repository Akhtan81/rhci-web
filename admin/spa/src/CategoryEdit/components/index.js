import React from 'react';
import {connect} from 'react-redux';
import {withRouter, Redirect} from 'react-router-dom';
import {FETCH_SUCCESS, MODEL_CHANGED} from '../actions';
import selectors from './selectors';
import SaveCategory from '../actions/SaveCategory';
import DeleteCategory from '../actions/DeleteCategory';
import FetchItem from '../actions/FetchItem';
import FetchItems from '../../Category/actions/FetchItems';
import translator from '../../translations/translator';
import {setTitle} from "../../Common/utils";

export const OrderTypes = [
    {
        value: 'junk_removal',
        label: translator('order_types_junk_removal')
    },
    {
        value: 'recycling',
        label: translator('order_types_recycling')
    },
    {
        value: 'donation',
        label: translator('order_types_donation')
    },
    {
        value: 'shredding',
        label: translator('order_types_shredding')
    },
]

class CategoryEdit extends React.Component {

    state = {
        canRedirect: false
    }

    componentWillMount() {

        const {items, filter} = this.props.Category
        if (items.length === 0) {
            this.props.dispatch(FetchItems(filter))
        }

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

        const {model} = this.props.CategoryEdit

        this.props.dispatch(DeleteCategory(model, () => {
           this.setState({
               canRedirect: true
           })
        }))
    }

    submit = () => {
        const {model} = this.props.CategoryEdit

        this.props.dispatch(SaveCategory(model))
    }

    change = (key, value) => this.props.dispatch({
        type: MODEL_CHANGED,
        payload: {
            [key]: value
        }
    })

    changeBool = name => e => this.change(name, e.target.checked)

    changeString = name => e => this.change(name, e.target.value)

    changeInt = name => e => {
        let value = parseInt(e.target.value.replace(/[^0-9]/g, ''))
        if (isNaN(value)) value = 0;

        this.change(name, value)
    }

    changeParent = e => {
        let match = null

        let id = parseInt(e.target.value.replace(/[^0-9]/g, ''))
        if (!isNaN(id) && id > 0) {
            match = id
        }

        this.change('parent', match)
    }

    getError = key => {
        const {errors} = this.props.CategoryEdit.validator

        if (errors[key] === undefined) return null

        return <small className="d-block c-red-500 form-text text-muted">{errors[key]}</small>
    }

    render() {

        const {model, isValid, isLoading, isSaveSuccess, serverErrors} = this.props.CategoryEdit
        const {items} = this.props.Category

        if (this.state.canRedirect) {
            return <Redirect to="/categories"/>
        }

        if (model.id) {
            setTitle('#' + model.id + ' ' + model.name)
        }

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
                            <label>{translator('parent_category')}</label>
                            <select name="parent"
                                    className="form-control"
                                    onChange={this.changeParent}
                                    value={model.parent ? model.parent : -1}>
                                <option value={-1}>
                                    ==={translator('no_parent_category')}===
                                </option>
                                {items.map((item, i) => {
                                    let lvl = ''
                                    for (let i = 0; i < item.lvl; i++) {
                                        lvl += ' - '
                                    }

                                    return <option
                                        key={i} value={item.id}
                                        disabled={item.id === model.id}>{lvl}{item.name}</option>
                                })}
                            </select>
                            {this.getError('parent')}
                        </div>

                        <div className="form-group">
                            <label className="required">{translator('name')}</label>
                            <input type="text"
                                   name="name"
                                   className="form-control"
                                   onChange={this.changeString('name')}
                                   value={model.name || ''}/>
                            {this.getError('name')}
                        </div>

                        <div className="form-group">
                            <label className="required">{translator('ordering')}</label>
                            <input type="number"
                                   name="ordering"
                                   className="form-control"
                                   onChange={this.changeInt('ordering')}
                                   value={model.ordering}/>
                            {this.getError('ordering')}
                        </div>

                        <div className="form-group">
                            <label className="required">{translator('type')}</label>
                            <select name="type"
                                    className="form-control"
                                    disabled={model.id > 0}
                                    onChange={this.changeString('type')}
                                    value={model.type}>
                                {OrderTypes.map((type, i) => <option key={i} value={type.value}>{type.label}</option>)}
                            </select>
                            {this.getError('type')}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(CategoryEdit))
