import React from 'react';
import {connect} from 'react-redux';
import {Link, withRouter} from 'react-router-dom';
import selectors from './selectors';
import translator from '../../translations/translator';
import FetchItems from '../actions/FetchItems';
import {dateFormat, setTitle} from '../../Common/utils';

class Index extends React.Component {

    componentWillMount() {

        setTitle(translator('navigation_units'))

        const {filter} = this.props.Unit

        this.props.dispatch(FetchItems(filter, 1, 0))
    }

    render() {

        return <div className="card my-3">

            <div className="card-header">
                <div className="row">
                    <div className="col">
                        <h4 className="m-0">
                            {translator('navigation_units')}
                        </h4>
                    </div>
                    <div className="col text-right">

                        <Link to="/units/new" className="btn btn-success btn-sm">
                            <i className="fa fa-plus"/>&nbsp;{translator('add')}
                        </Link>
                    </div>
                </div>
            </div>
            <div className="card-body">
                {this.renderItems()}
            </div>
        </div>
    }

    renderItems = () => {

        const {items, isLoading} = this.props.Unit

        if (!isLoading && items.length === 0) {
            return <div className="banner">
                <h3>{translator('no_units_title')}</h3>
                <h4>{translator('no_units_footer')}</h4>
            </div>
        }

        return <div className="table-responsive mb-3">
            <table className="table table-sm table-hover">
                <thead>
                <tr>
                    <th>{translator('name')}</th>
                    <th>{translator('created_at')}</th>
                </tr>
                </thead>

                <tbody>{items.map(this.renderChild)}</tbody>
            </table>
        </div>
    }

    renderChild = (model, key) => {
        return <tr key={key}>
            <td className="no-wrap">
                <Link to={'/units/' + model.id}>{model.name}</Link>
            </td>
            <td className="text-nowrap">{dateFormat(model.createdAt)}</td>
        </tr>
    }
}

export default withRouter(connect(selectors)(Index))
