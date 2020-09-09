import React from 'react';
import {connect} from 'react-redux';
import {Redirect, withRouter} from 'react-router-dom';
import {FETCH_SUCCESS, MODEL_CHANGED, SET_ACTIVE_LOCALE} from '../actions';
import selectors from './selectors';
import SaveCategory from '../actions/SaveCategory';
import DeleteCategory from '../actions/DeleteCategory';
import FetchItem from '../actions/FetchItem';
import translator from '../../translations/translator';
import {isMobile, objectValues, setTitle} from "../../Common/utils";

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
    {
        value: 'busybee',
        label: translator('order_types_busybee')
    },
]

class CategoryEdit extends React.Component {

    state = {
        isMobile: isMobile(),
        canRedirect: false
    }

    componentWillMount() {

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

        if (typeof window !== 'undefined') {
            window.addEventListener('resize', this.resizeHandler, false);
        }
    }

    componentWillUnmount() {
        if (typeof window !== 'undefined') {
            window.removeEventListener('resize', this.resizeHandler, false);
        }
    }

    resizeHandler = () => {
        this.setState({
            isMobile: isMobile()
        })
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

    changeString = name => e => this.change(name, e.target.value)

    changeTranslationString = (locale, name) => e => {
        this.props.dispatch({
            type: MODEL_CHANGED,
            payload: {
                translation: {
                    locale,
                    [name]: e.target.value
                }
            }
        })
    }

    setActiveLocale = payload => () => {
        this.props.dispatch({
            type: SET_ACTIVE_LOCALE,
            payload
        })
    }

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

    getTranslationError = (locale, key) => {
        const {errors} = this.props.CategoryEdit.validator

        if (errors.trans === undefined) return null
        if (errors.trans[locale] === undefined) return null
        if (errors.trans[locale][key] === undefined) return null

        return <small className="d-block c-red-500 form-text text-muted">{errors.trans[locale][key]}</small>
    }

    renderTranslations() {

        const {model, activeLocale} = this.props.CategoryEdit

        const translations = objectValues(model.translations)

        const small = this.state.isMobile

        return <div className="row">
            <div className="col-12">

                <ul className="nav nav-tabs mb-2 text-center">
                    {translations.map((translation, key) => {

                        return <li key={key} className={"nav-item" + (small ? " w-100" : "")}>
                            <div className={"nav-link" + (activeLocale === translation.locale ? ' active' : '')}
                                 onClick={this.setActiveLocale(translation.locale)}>
                                {translation.locale}
                            </div>
                        </li>
                    })}
                </ul>

                <div className="nav-content">
                    {translations.filter(translation => translation.locale === activeLocale).map((translation, key) => {

                        return <div key={key} className="form-group">
                            <label className="required">{translator('name')}</label>
                            <input type="text"
                                   name="name"
                                   className="form-control"
                                   onChange={this.changeTranslationString(translation.locale, 'name')}
                                   value={translation.name || ''}/>
                            {this.getTranslationError(translation.locale, 'name')}
                        </div>
                    })}
                </div>
            </div>
        </div>
    }

    render() {

        const {model, isValid, isLoading, isSaveSuccess, serverErrors} = this.props.CategoryEdit
        const {items} = this.props.Category

        if (this.state.canRedirect) {
            return <Redirect to="/categories"/>
        }

        const translation = model.translations[AppParameters.locale]

        if (model.id && translation) {
            if (translation.name) {
                setTitle('#' + model.id + ' ' + translation.name)
            } else {
                setTitle('#' + model.id)
            }
        }

        return <div className="card my-3">

            <div className="card-header">
                <div className="row">
                    <div className="col">
                        <h4 className="m-0">
                            {translator('navigation_categories')}&nbsp;/&nbsp;
                            {model.id && translation
                                ? <span>#{model.id}&nbsp;{translation.name || ''}</span>
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
                                    disabled={model.id > 0}
                                    onChange={this.changeString('type')}
                                    value={model.type || -1}>
                                <option value={-1} disabled={true}>{translator('select_type')}</option>
                                {OrderTypes.map((type, i) => <option key={i} value={type.value}>{type.label}</option>)}
                            </select>
                            {this.getError('type')}
                        </div>

                        <div className="form-group">
                            <label>{translator('parent_category')}</label>
                            <select name="parent"
                                    className="form-control"
                                    disabled={!model.type}
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
                            <label className="required">{translator('ordering')}</label>
                            <input type="number"
                                   name="ordering"
                                   className="form-control"
                                   onChange={this.changeInt('ordering')}
                                   value={model.ordering}/>
                            {this.getError('ordering')}
                        </div>

                        {this.renderTranslations()}
                    </div>
                </div>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(CategoryEdit))
