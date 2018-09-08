import React from 'react'
import Props from 'prop-types'
import ReactPaginate from 'react-paginate'

class Paginator extends React.Component {

    constructor() {
        super()
        this.changePage = this.changePage.bind(this)
    }

    getTotalPages() {
        const pagination = this.props
        let pages = 0;
        if (pagination.limit > 0) {
            let split = pagination.total / pagination.limit;
            pages = parseInt(split);
            if (split % 1 !== 0 && pagination.total > pagination.limit) {
                pages += 1;
            }
        }

        return pages;
    }

    changePage(e) {
        this.props.onChange(++e.selected)
    }

    render() {
        const pages = this.getTotalPages()
        if (pages <= 1) return null

        return <ReactPaginate
            previousLabel={'<'}
            nextLabel={'>'}
            breakLabel={<a href="javascript:">...</a>}
            pageCount={pages}
            forcePage={this.props.page - 1}
            onPageChange={this.changePage}
            marginPagesDisplayed={3}
            pageRangeDisplayed={5}
            nextClassName={"page-item"}
            nextLinkClassName={"page-link"}
            previousClassName={"page-item"}
            previousLinkClassName={"page-link"}
            breakClassName={"page-item"}
            pageClassName={"page-item"}
            pageLinkClassName={"page-link"}
            containerClassName={"pagination"}
            subContainerClassName={"pages pagination"}
            activeClassName={"active"}
        />
    }
}

Paginator.propTypes = {
    onChange: Props.func.isRequired,
    total: Props.number.isRequired,
    limit: Props.number.isRequired,
    page: Props.number.isRequired,
}

export default Paginator