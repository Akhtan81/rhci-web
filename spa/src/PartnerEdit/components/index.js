import React from 'react';
import {connect} from 'react-redux';
import {withRouter, Redirect} from 'react-router-dom';
import {MODEL_CHANGED, FETCH_SUCCESS, ADD_POSTAL_CODE, REMOVE_POSTAL_CODE} from '../actions';
import selectors from './selectors';
import Save from '../actions/Save';
import UploadMedia from '../actions/UploadMedia';
import FetchItem from '../actions/FetchItem';
import translator from '../../translations/translator';
import {cid, dateFormat, objectValues, setTitle} from "../../Common/utils";

// import FetchCountries from "../../Partner/actions/FetchCountries";

class PartnerEdit extends React.Component {

    state = {
        canRedirect: false
    }

    componentWillMount() {

        // const {Country} = this.props
        // if (!Country.isLoading && Country.items.length === 0) {
        //     this.props.dispatch(FetchCountries())
        // }

        const {id} = this.props.match.params
        if (id > 0) {

            setTitle(translator('loading'))

            this.props.dispatch(FetchItem(id, () => {
                this.setState({canRedirect: true})
            }))
        } else {

            setTitle(translator('navigation_partners_new'))

            this.props.dispatch({
                type: FETCH_SUCCESS,
                payload: {}
            })
        }
    }

    submit = () => {
        const {model} = this.props.PartnerEdit

        this.props.dispatch(Save(model, () => {
            this.setState({canRedirect: true})
        }))
    }

    reject = () => {

        if (!confirm(translator('confirm_partner_deactivation'))) return;

        const {model} = this.props.PartnerEdit

        this.props.dispatch(Save({
            id: model.id,
            status: 'rejected',
            user: {
                isActive: false
            }
        }))
    }

    approve = () => {

        const {model} = this.props.PartnerEdit

        this.props.dispatch(Save({
            id: model.id,
            postalCodes: model.postalCodes,
            status: 'approved',
            user: {
                isActive: true
            }
        }))
    }

    deactivate = () => {

        if (!confirm(translator('confirm_partner_deactivation'))) return;

        const {model} = this.props.PartnerEdit

        this.props.dispatch(Save({
            id: model.id,
            user: {
                isActive: false
            }
        }))
    }

    activate = () => {

        const {model} = this.props.PartnerEdit

        this.props.dispatch(Save({
            id: model.id,
            user: {
                isActive: true
            }
        }))

    }

    change = (key, value = null) => this.props.dispatch({
        type: MODEL_CHANGED,
        payload: {
            [key]: value
        }
    })

    changeRequestType = (cid) => e => {

        let value = e.target.value
        if (value === 'none') value = null

        this.props.dispatch({
            type: MODEL_CHANGED,
            payload: {
                request: {
                    cid,
                    type: value
                }
            }
        })
    }

    changeRequestPostalCode = cid => e => {

        this.props.dispatch({
            type: MODEL_CHANGED,
            payload: {
                request: {
                    cid,
                    postalCode: e.target.value.replace(/[^0-9]/g, '')
                }
            }
        })
    }

    removePostalCode = cid => () => {

        this.props.dispatch({
            type: REMOVE_POSTAL_CODE,
            payload: {
                cid,
            }
        })
    }

    addPostalCode = () => {

        this.props.dispatch({
            type: ADD_POSTAL_CODE,
            payload: {
                cid: cid(),
                postalCode: null,
                type: null
            }
        })
    }

    // changeCountry = e => {
    //     let value = parseInt(e.target.value.replace(/[^0-9]/g, ''))
    //     if (isNaN(value) || value < 0) {
    //         value = null;
    //     } else {
    //         const {items} = this.props.Country
    //
    //         value = items.find(item => item.id === value);
    //     }
    //
    //     this.change('country', value)
    // }

    changeString = name => e => this.change(name, e.target.value)

    changeFloat = name => e => {
        let value = parseFloat(e.target.value.replace(/[^0-9\.]/g, ''))
        if (isNaN(value)) value = 0

        this.change(name, value)
    }

    uploadAvatar = (e) => {
        const file = e.target.files[0]
        if (!file) return

        this.props.dispatch(UploadMedia(file))
    }

    getError = key => {
        const {errors} = this.props.PartnerEdit.validator

        if (errors[key] === undefined) return null

        return <small className="d-block c-red-500 form-text text-muted">{errors[key]}</small>
    }

    renderActions = () => {

        const {model, isValid, isLoading} = this.props.PartnerEdit

        if (!model.id) return null

        const actions = [];

        switch (model.status) {
            case 'created':

                actions.push(<button
                    key={1}
                    className="btn btn-outline-danger btn-sm mr-2"
                    onClick={this.reject}
                    disabled={isLoading}>
                    <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-thumbs-down"}/>
                    &nbsp;{translator('partner_reject')}
                </button>)

                actions.push(<button
                    key={2}
                    className="btn btn-outline-success btn-sm mr-2"
                    onClick={this.approve}
                    disabled={isLoading || !isValid}>
                    <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-thumbs-up"}/>
                    &nbsp;{translator('partner_approve')}
                </button>)

                break;
            default:

                if (model.user.isActive) {
                    actions.push(<button
                        key={3}
                        className="btn btn-outline-danger btn-sm mr-2"
                        onClick={this.deactivate}
                        disabled={isLoading}>
                        <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-thumbs-down"}/>
                        &nbsp;{translator('deactivate')}
                    </button>)
                } else {
                    actions.push(<button
                        key={4}
                        className="btn btn-outline-success btn-sm mr-2"
                        onClick={this.activate}
                        disabled={isLoading || !isValid}>
                        <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-thumbs-up"}/>
                        &nbsp;{translator('activate')}
                    </button>)
                }
        }

        return actions

    }

    renderRequestedCodes = () => {

        const {model} = this.props.PartnerEdit

        if (model.requests.length === 0) {
            return <p className="text-center help-block">
                {translator('no_requested_postal_codes')}
            </p>
        }

        return <ul>{model.requests.map((request, i) => {
                let name = ''

                switch (request.type) {
                    case 'recycling':
                        name = translator('order_types_recycling')
                        break;
                    case 'junk_removal':
                        name = translator('order_types_junk_removal')
                        break;
                    case 'shredding':
                        name = translator('order_types_shredding')
                        break;
                }

                return <li key={i}>{request.postalCode} - {name}</li>
            }
        )}</ul>
    }

    renderAssignedPostalCodes() {

        const {model} = this.props.PartnerEdit

        const codes = objectValues(model.postalCodes)

        return <div className="row">

            {codes.map((code, i) => {

                return <div key={i}
                            className="col-12 col-md-10 col-lg-8 offset-md-1 offset-lg-2">

                    <div className="form-group">
                        <div className="input-group">

                            {codes.length > 1 ?
                                <div className="input-group-prepend">
                                    <button className="btn btn-outline-secondary"
                                            onClick={this.removePostalCode(code.cid)}>
                                        <i className="fa fa-times"/>
                                    </button>
                                </div> : null}

                            <input type="text"
                                   name="postalCode"
                                   className="form-control"
                                   placeholder={translator('postal_code')}
                                   onChange={this.changeRequestPostalCode(code.cid)}
                                   value={code.postalCode || ""}/>

                            <div className="input-group-append">
                                <select name="type"
                                        value={code.type || 'none'}
                                        onChange={this.changeRequestType(code.cid)}
                                        className="form-control">
                                    <option value="none">{translator("select_type")}</option>
                                    <option value="junk_removal">{translator("order_types_junk_removal")}</option>
                                    <option value="recycling">{translator("order_types_recycling")}</option>
                                    <option disabled={true}
                                            value="shredding">{translator("order_types_shredding")}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
            })}
        </div>
    }

    render() {

        const {model, isValid, isLoading, isSaveSuccess, serverErrors} = this.props.PartnerEdit
        // const {Country} = this.props

        if (this.state.canRedirect) {
            return <Redirect to="/partners"/>
        }

        if (model.id) {
            setTitle('#' + model.id + " " + model.user.name)
        }

        return <div className="bgc-white bd bdrs-3 p-20 my-3">

            <div className="row mb-3">
                <div className="col-12 col-lg-6">
                    <h4 className="page-title">
                        {translator('navigation_partners')}&nbsp;/&nbsp;
                        {model.id > 0 ? <span>#{model.id}</span> : <span>{translator('create')}</span>}
                    </h4>

                    {model.createdAt ? <p className="help-block">
                        {translator("created_at")}:&nbsp;{dateFormat(model.createdAt)}
                        </p> : null}

                </div>
                <div className="col-12 col-lg-6 text-right">

                    {this.renderActions()}

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

                    <div className="row">
                        <div className="col-12 col-md-6 col-lg-3">

                            <div className="img-container">
                                {!isLoading && model.user.avatar
                                    ? <img src={model.user.avatar.url} className="img-fluid"/>
                                    : null}
                            </div>

                            <div className="form-group">
                                <label>{translator('avatar')}</label>
                                <input type="file"
                                       name="avatar"
                                       className="form-control"
                                       accept="image/png,image/jpg,image/jpeg,image/gif,image/bmp"
                                       onChange={this.uploadAvatar}/>
                                {this.getError('avatar')}
                            </div>

                        </div>

                        <div className="col-12 col-md-6 col-lg-5">

                            <h4>{translator('personal_information')}</h4>

                            <div className="row">
                                <div className="col-12">
                                    <div className="form-group">
                                        <label className="required">{translator('name')}</label>
                                        <input type="text"
                                               name="name"
                                               className="form-control"
                                               onChange={this.changeString('name')}
                                               value={model.user.name || ''}/>
                                        {this.getError('name')}
                                    </div>
                                </div>
                                <div className="col">
                                    <div className="form-group">
                                        <label className="required">{translator('email')}</label>
                                        <input type="email"
                                               name="email"
                                               className="form-control"
                                               onChange={this.changeString('email')}
                                               value={model.user.email || ''}/>
                                        {this.getError('email')}
                                    </div>
                                </div>
                                <div className="col">

                                    <div className="form-group">
                                        <label>{translator('phone')}</label>
                                        <input type="text"
                                               name="phone"
                                               className="form-control"
                                               onChange={this.changeString('phone')}
                                               value={model.user.phone || ''}/>
                                        {this.getError('phone')}
                                    </div>
                                </div>
                            </div>

                            <div className="row">
                                <div className="col-12 col-sm-6">

                                    <div className="form-group">
                                        <label className={!model.id ? "required" : ""}>{translator('password')}</label>
                                        <input type="password"
                                               name="password"
                                               className="form-control"
                                               onChange={this.changeString('password')}
                                               value={model.user.password || ''}/>
                                        {this.getError('password')}
                                    </div>

                                </div>
                                <div className="col-12 col-sm-6">

                                    <div className="form-group">
                                        <label
                                            className={!model.id ? "required" : ""}>{translator('password_repeat')}</label>
                                        <input type="password"
                                               name="password2"
                                               className="form-control"
                                               onChange={this.changeString('password2')}
                                               value={model.user.password2 || ''}/>
                                        {this.getError('password2')}
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div className="col-12 col-md-6 col-lg-4">

                            <h4>{translator('location')}</h4>

                            <div className="row">
                                <div className="col-12 col-lg-6">
                                    <div className="form-group">
                                        <label className="required">{translator('address')}</label>
                                        <input type="text"
                                               name="address"
                                               className="form-control"
                                               onChange={this.changeString('address')}
                                               value={model.location.address || ''}/>
                                        {this.getError('address')}
                                    </div>
                                </div>
                                <div className="col-12 col-lg-6">
                                    <div className="form-group">
                                        <label className="required">{translator('postal_code')}</label>
                                        <input type="text"
                                               name="postalCode"
                                               className="form-control"
                                               onChange={this.changeString('postalCode')}
                                               value={model.location.postalCode || ''}/>
                                        {this.getError('postalCode')}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="row">
                        <div className="col-12 col-lg-6">

                            <h4>{translator('requested_postal_codes')}</h4>

                            <div className="row">
                                <div className="col-12">

                                    {this.renderRequestedCodes()}

                                </div>
                            </div>
                        </div>

                        <div className="col-12 col-lg-6">

                            <h4>{translator('assigned_postal_codes')}</h4>

                            <div className="row">
                                <div className="col-12">

                                    {this.renderAssignedPostalCodes()}

                                </div>
                            </div>

                            <div className="row">
                                <div className="col-12 col-md-10 col-lg-8 offset-md-1 offset-lg-2">
                                    <div className="form-group text-right">
                                        <button className="btn btn-sm btn-outline-success"
                                                onClick={this.addPostalCode}>
                                            <i className="fa fa-plus"/>&nbsp;{translator('add')}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(PartnerEdit))
