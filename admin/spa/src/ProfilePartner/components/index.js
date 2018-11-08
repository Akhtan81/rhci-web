import React from 'react';
import {connect} from 'react-redux';
import {withRouter} from 'react-router-dom';

import selectors from './selectors';
import FetchItem from '../actions/FetchItem';
import Save from '../actions/Save';
import translator from '../../translations/translator';
import {setTitle} from "../../Common/utils";
import PaymentInfo from "./PaymentInfo";
import PaymentInfoRecycling from "./PaymentInfoRecycling";
import {MODEL_CHANGED} from "../actions";

class ProfilePartner extends React.Component {

    componentWillMount() {

        setTitle(translator('navigation_profile'))

        this.props.dispatch(FetchItem())
    }

    getError = key => {
        const {errors} = this.props.ProfilePartner.validator

        if (errors[key] === undefined) return null

        return <small className="d-block c-red-500 form-text text-muted">{errors[key]}</small>
    }

    submit = () => {
        const {model} = this.props.ProfilePartner

        this.props.dispatch(Save(model))
    }

    change = (key, value = null) => this.props.dispatch({
        type: MODEL_CHANGED,
        payload: {
            [key]: value
        }
    })

    changeString = name => e => this.change(name, e.target.value)

    renderRequestedPostalCodes() {

        const {model} = this.props.ProfilePartner

        if (model.requests.length === 0) return <span>{translator('no_requested_postal_codes')}</span>

        return <ul className="simple">{model.requests.map((item, i) =>
            <li key={i}>{item.postalCode} - {translator('order_types_' + item.type)}</li>)}
        </ul>
    }

    renderAssignedPostalCodes() {

        const {model} = this.props.ProfilePartner

        const hasAccount = model.hasAccount && model.customerId
        const hasCard = model.hasCard

        const recyclingIcon = <i className={"fa " + (hasCard ? "fa-check" : "fa-lock")}/>
        const junkRemovalIcon = <i className={"fa " + (hasAccount ? "fa-check" : "fa-lock")}/>

        return <div className="row">
            <div className="col-12 col-md-4">
                <h5><i className="fa fa-cubes"/>&nbsp;{translator('order_types_junk_removal')}</h5>

                {model.id && !hasAccount ? <p className="c-red-500">
                    <i className="fa fa-warning"/>&nbsp;{translator('no_account_for_junk_removal')}
                </p> : null}

                {model.postalCodesJunkRemoval.length > 0
                    ? <ul className="simple">{model.postalCodesJunkRemoval.map((item, i) =>
                        <li key={i}>{junkRemovalIcon}&nbsp;{item}
                        </li>)}
                    </ul>
                    : <span>{translator('no_assigned_postal_codes')}</span>}
            </div>
            <div className="col-12 col-md-4">
                <h5><i className="fa fa-recycle"/>&nbsp;{translator('order_types_recycling')}</h5>

                {model.id && !hasCard ? <p className="c-red-500">
                    <i className="fa fa-warning"/>&nbsp;{translator('no_account_recycling')}
                </p> : null}

                {model.postalCodesRecycling.length > 0
                    ? <ul className="simple">{model.postalCodesRecycling.map((item, i) =>
                        <li key={i}>{recyclingIcon}&nbsp;{item}</li>)}
                    </ul>
                    : <span>{translator('no_assigned_postal_codes')}</span>}
            </div>
            <div className="col-12 col-md-4">
                <h5><i className="fa fa-stack-overflow"/>&nbsp;{translator('order_types_shredding')}</h5>

                {model.id && !hasAccount ? <p className="c-red-500">
                    <i className="fa fa-warning"/>&nbsp;{translator('no_account_for_shredding')}
                </p> : null}

                {model.postalCodesShredding.length > 0
                    ? <ul className="simple">{model.postalCodesShredding.map((item, i) =>
                        <li key={i}>{junkRemovalIcon}&nbsp;{item}</li>)}
                    </ul>
                    : <span>{translator('no_assigned_postal_codes')}</span>}
            </div>
        </div>
    }

    renderProfileForm = () => {

        const {model} = this.props.ProfilePartner

        return <div className="row">
            <div className="col-12">
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
                    <div className="col-12 col-sm-4">

                        <div className="form-group">
                            <label className={!model.id ? "required" : ""}>{translator('current_password')}</label>
                            <input type="password"
                                   name="currentPassword"
                                   className="form-control"
                                   onChange={this.changeString('currentPassword')}
                                   value={model.user.currentPassword || ''}/>
                            {this.getError('currentPassword')}
                        </div>

                    </div>
                    <div className="col-12 col-sm-4">

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
                    <div className="col-12 col-sm-4">

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
        </div>
    }

    render() {

        const {model, isSaveSuccess, serverErrors, isLoading, isValid} = this.props.ProfilePartner

        let location = ''
        if (model.location) {
            const items = []
            if (model.location.city) {
                items.push(model.location.city);
            }
            if (model.location.address) {
                items.push(model.location.address);
            }
            if (model.location.postalCode) {
                items.push(model.location.postalCode);
            }

            location = items.join(', ')
        }

        return <div>

            <div className="bgc-white bd bdrs-3 p-20 my-3">

                <div className="row mb-3">
                    <div className="col-6">
                        <h4 className="page-title">{translator('navigation_profile')}</h4>
                    </div>
                    <div className="col-6 text-right">

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

                <div className="row mb-4">
                    <div className="col">

                        {serverErrors.length > 0 && <div className="alert alert-danger">
                            <ul className="simple">{serverErrors.map((e, i) => <li key={i}>{e}</li>)}</ul>
                        </div>}

                        <div className="row mb-4">

                            <div className="col-12">
                                <div className="row">
                                    <div className="col-12 col-md-8 offset-md-2 col-xl-6 offset-xl-3">
                                        {this.renderProfileForm()}

                                        {model.country ?
                                            <h4><i className="fa fa-globe"/>&nbsp;{model.country.name}</h4> : null}

                                        {location ?
                                            <h4><i className="fa fa-map-marker"/>&nbsp;{location}</h4> : null}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="row">
                            <div className="col-12">
                                <div className="row">
                                    <div className="col-12 col-md-3">
                                        <h4>{translator('requested_postal_codes')}</h4>
                                        {this.renderRequestedPostalCodes()}
                                    </div>
                                    <div className="col-12 col-md-9">
                                        <h4>{translator('assigned_postal_codes')}</h4>
                                        {this.renderAssignedPostalCodes()}
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <PaymentInfo/>

            {/*<PaymentInfoRecycling/>*/}
        </div>
    }
}

export default withRouter(connect(selectors)(ProfilePartner))
