import React from 'react';
import {connect} from 'react-redux';
import {withRouter, Redirect} from 'react-router-dom';
import {FETCH_SUCCESS, MODEL_CHANGED, SET_ACTIVE_LOCALE} from '../actions';
import selectors from './selectors';
import SaveCategory from '../actions/Save';
import DeleteItem from '../actions/DeleteItem';
import FetchItem from '../actions/FetchItem';
import translator from '../../translations/translator';
import {objectValues, setTitle} from "../../Common/utils";

class UnitEdit extends React.Component {

    state = {
        canRedirect: false
    }

    componentWillMount() {

        const {id} = this.props.match.params
        if (id > 0) {

            setTitle(translator('loading'))

            this.props.dispatch(FetchItem(id))
        } else {

            setTitle(translator('navigation_units_new'))

            this.props.dispatch({
                type: FETCH_SUCCESS,
                payload: {}
            })
        }
    }

    remove = () => {
        if (!confirm(translator('confirm_delete'))) return

        const {model} = this.props.UnitEdit

        this.props.dispatch(DeleteItem(model, () => {
            this.setState({
                canRedirect: true
            })
        }))
    }

    submit = () => {
        const {model} = this.props.UnitEdit

        this.props.dispatch(SaveCategory(model))
    }

    change = (key, value) => this.props.dispatch({
        type: MODEL_CHANGED,
        payload: {
            [key]: value
        }
    })

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

    getError = key => {
        const {errors} = this.props.UnitEdit.validator

        if (errors[key] === undefined) return null

        return <small className="d-block c-red-500 form-text text-muted">{errors[key]}</small>
    }

    getTranslationError = (locale, key) => {
        const {errors} = this.props.UnitEdit.validator

        if (errors.trans === undefined) return null
        if (errors.trans[locale] === undefined) return null
        if (errors.trans[locale][key] === undefined) return null

        return <small className="d-block c-red-500 form-text text-muted">{errors.trans[locale][key]}</small>
    }

    renderTranslations() {

        const {model, activeLocale} = this.props.UnitEdit

        const translations = objectValues(model.translations)

        return <div className="row">
            <div className="col-12">

                <ul className="nav nav-tabs mb-2">
                    {translations.map((translation, key) => {

                        return <li key={key} className="nav-item">
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

        const {model, isValid, isLoading, isSaveSuccess, serverErrors} = this.props.UnitEdit

        if (this.state.canRedirect) {
            return <Redirect to="/units"/>
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
                            {translator('navigation_units')}&nbsp;/&nbsp;
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

                        {this.renderTranslations()}

                    </div>
                </div>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(UnitEdit))
