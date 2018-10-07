import reducer from '../../../src/Category/reducers'

describe('`Category` index reducer', () => {

    it('`filter` should return the initial state', () => {
        expect(reducer(undefined, {}).filter).toEqual({
            type: 'junk_removal',
            locale: 'en'
        })
    })

    it('`items` should return the initial state', () => {
        expect(reducer(undefined, {}).items).toEqual([])
    })

    it('`isLoading` should return the initial state', () => {
        expect(reducer(undefined, {}).isLoading).toEqual(false)
    })

})