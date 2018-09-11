import React from 'react';
import {connect} from 'react-redux';
import {withRouter} from 'react-router-dom';
import {MODEL_CHANGED, FETCH_SUCCESS} from '../actions';
import selectors from './selectors';
import SaveCategory from '../actions/SaveCategory';
import DeleteCategory from '../actions/DeleteCategory';
import FetchItem from '../actions/FetchItem';
import FetchItems from '../../Category/actions/FetchItems';
import translator from '../../translations/translator';

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
        value: 'shredding',
        label: translator('order_types_shredding')
    },
]

class CategoryEdit extends React.Component {

    componentWillMount() {

        const {items, filter} = this.props.Category
        if (items.length === 0) {
            this.props.dispatch(FetchItems(filter))
        }

        const {id} = this.props.match.params
        if (id > 0) {
            this.props.dispatch(FetchItem(id))
        } else {
            this.props.dispatch({
                type: FETCH_SUCCESS,
                payload: {}
            })
        }
    }

    remove = () => {
        if (!confirm(translator('confirm_delete'))) return

        const {model} = this.props.CategoryEdit

        this.props.dispatch(DeleteCategory(model))
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

        return <div className="bgc-white bd bdrs-3 p-20 mB-20">

            <div className="row">
                <div className="col">
                    <h4 className="page-title">
                        {translator('navigation_categories')}&nbsp;/&nbsp;
                        {model.id > 0 ? <span>#{model.id}&nbsp;{model.name}</span> :
                            <span>{translator('create')}</span>}
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
                        <label className="required">{translator('locale')}</label>
                        <select name="locale"
                                className="form-control"
                                disabled={model.id > 0}
                                onChange={this.changeString('locale')}
                                value={model.locale}>
                            {AppParameters.locales.map((code, i) => <option key={i} value={code}>{code}</option>)}
                        </select>
                        {this.getError('locale')}
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

                    <div className="form-group">
                        <label>
                            <input type="checkbox"
                                   name="isSelectable"
                                   onChange={this.changeBool('isSelectable')}
                                   checked={model.isSelectable}/>
                            &nbsp;{translator('is_selectable')}
                        </label>
                        {this.getError('isSelectable')}
                    </div>

                    <div className="form-group">
                        <label>
                            <input type="checkbox"
                                   name="hasPrice"
                                   onChange={this.changeBool('hasPrice')}
                                   checked={model.hasPrice}/>
                            &nbsp;{translator('has_price')}
                        </label>
                        {this.getError('hasPrice')}
                    </div>

                    <div className="form-group">
                        <label className={model.hasPrice ? 'required' : ''}>{translator('price')}</label>
                        <input type="number"
                               name="price"
                               min={0}
                               step={1}
                               className="form-control"
                               onChange={this.changeInt('price')}
                               value={model.price !== null ? model.price : ''}/>
                        {this.getError('price')}
                        <small className="d-block text-muted">
                            <i className="fa fa-info-circle"/>&nbsp;{translator('price_notice')}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(CategoryEdit))
