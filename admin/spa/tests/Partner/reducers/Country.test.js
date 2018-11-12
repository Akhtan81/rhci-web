import reducer from '../../../src/Partner/reducers/Country'

describe('`Partner` `Country` reducer', () => {

    it('`items` should return the initial state', () => {
        expect(reducer(undefined, {}).items).toEqual([])
    })

    it('`isLoading` should return the initial state', () => {
        expect(reducer(undefined, {}).isLoading).toEqual(false)
    })
})