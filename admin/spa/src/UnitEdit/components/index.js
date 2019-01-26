import React from 'react';
import {connect} from 'react-redux';
import {withRouter, Redirect} from 'react-router-dom';
import {FETCH_SUCCESS, MODEL_CHANGED} from '../actions';
import selectors from './selectors';
import SaveCategory from '../actions/Save';
import DeleteItem from '../actions/DeleteItem';
import FetchItem from '../actions/FetchItem';
import translator from '../../translations/translator';
import {setTitle} from "../../Common/utils";

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

    changeString = name => e => this.change(name, e.target.value)

    getError = key => {
        const {errors} = this.props.UnitEdit.validator

        if (errors[key] === undefined) return null

        return <small className="d-block c-red-500 form-text text-muted">{errors[key]}</small>
    }

    render() {

        const {model, isValid, isLoading, isSaveSuccess, serverErrors} = this.props.UnitEdit

        if (this.state.canRedirect) {
            return <Redirect to="/units"/>
        }

        if (model.id) {
            setTitle('#' + model.id + ' ' + model.name)
        }

        return <div className="card my-3">

            <div className="card-header">
                <div className="row">
                    <div className="col">
                        <h4 className="m-0">
                            {translator('navigation_units')}&nbsp;/&nbsp;
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
                            <label className="required">{translator('name')}</label>
                            <input type="text"
                                   name="name"
                                   className="form-control"
                                   onChange={this.changeString('name')}
                                   value={model.name || ''}/>
                            {this.getError('name')}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(UnitEdit))
