import reducer from '../../../src/Partner/reducers'

describe('`Partner` index reducer', () => {

    it('`filter` should return the initial state', () => {
        expect(reducer(undefined, {}).filter).toEqual({statuses: 'created,approved'})
    })

    it('`page` should return the initial state', () => {
        expect(reducer(undefined, {}).page).toEqual(1)
    })

    it('`total` should return the initial state', () => {
        expect(reducer(undefined, {}).total).toEqual(0)
    })

    it('`limit` should return the initial state', () => {
        expect(reducer(undefined, {}).limit).toEqual(0)
    })

    it('`items` should return the initial state', () => {
        expect(reducer(undefined, {}).items).toEqual([])
    })

    it('`isLoading` should return the initial state', () => {
        expect(reducer(undefined, {}).isLoading).toEqual(false)
    })
})